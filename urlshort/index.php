<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="author" content="@OperativeThunny" />
    <meta
      name="description"
      content="This is a simple URL shortener built with HTML, CSS, JavaScript, and PocketBase."
    />
    <title>Verb Is URL Shortener</title>

    <!-- import bootsrap css -->
    <link rel="stylesheet" href="/css/bootstrap.min.css" />
  </head>
  <body>
    <div class="container">
      <header class="header">
        <h1>Verb Is... A URL Shortener!</h1>
      </header>
    </div>
    <div class="container">
      <div class="row">
        
        <nav class="col">
          <ul>
            <li><a href="/">Home</a></li>
            <li><a href="/tt">Term Term</a></li>
            <li><a href="/contact">Contact</a></li>
          </ul>
        </nav>

        <main class="col">
          <section>
            <h2>Get'cher URL Shortener here!</h2>
            <p>Fill out the below form to create a shortened URL.</p>
            <form action="/s/" method="POST">
              <label for="url">URL:</label>
              <input type="url" id="url" name="url" required />
              <button id="submiturl" type="submit">Shorten</button>
          </section>
        </main>

      </div>
    </div>
    <div class="container">
      <footer>
        <p>Copyright &copy; 2024</p>
      </footer>
    </div>
    
    <script src="/js/bootstrap.min.js"></script>
    <!-- import jquery from cdn -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="/pocketbasesdk/js-sdk-0.21.4/dist/pocketbase.umd.js"></script>
    <script type="text/javascript">
      
      function get_random_string(length) {
        if (!length) {
          length = 5;
        }
        const randomChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var result = '';
        for (var i = 0; i < length; i++) {
          result += randomChars.charAt(Math.floor(Math.random() * randomChars.length));
        }
        return result;
      }

      const pb = new PocketBase("https://vrb.is/s");
      //console.log(pb);
      const submiturl = document.getElementById("submiturl");
      submiturl.addEventListener("click", async (e) => {
        e.preventDefault();
        const url = document.getElementById("url").value;

        // check key is unique
        let key = false;
        let admin_key = false;
        let keyResult = false;
        let adminKeyResult = false;
        // I wish I coild do this server side, but for some reason the
        // PocketBase SDK JS hooks are not working on the server side, even
        // though the set up is in accordance with the documentation.
        do {
          key = get_random_string(5);
          admin_key = get_random_string(12);
          try {
            keyResult = await pb.collection('URL').getFirstListItem('key="'+key+'"');
            adminKeyResult = await pb.collection('URL').getFirstListItem('admin_key="'+admin_key+'"');
          } catch (e) {
            console.log("generated key not found, so we are good to go!");
          }
        } while (keyResult || adminKeyResult);

        const data = {
          "is_active": 1,
          "target": url,
          "key": key,
          "admin_key": admin_key,
        };

        try {
          const record = await pb.collection('URL').create(data);
          console.log(record);
          // display the shortened URL using a bootstrap alert
          const alert = document.createElement('div');
          alert.classList.add('alert', 'alert-success');
          alert.setAttribute('role', 'alert');
          alert.innerHTML = 'Shortened URL: <a href="https://vrb.is/'+record.key+'">https://vrb.is/'+record.key+'</a> ( Admin Key for stats/deletion: <a href="https://vrb.is/admin/' + record.admin_key + '">' + record.admin_key + '</a> )';
          document.body.appendChild(alert);

        } catch (e) {
          console.error(e);
          // show bootstrap alert with error message
        }
        
      });

      
      window.addEventListener("load", async () => {
        // get the key from the URL, issue redirect after incrementing the click count
        let key = window.location.pathname;
        key = key.substring(1)
        
        if (key.length > 1 && !key.includes('admin')) {
          try {
            const record = await pb.collection('URL').getFirstListItem('key="'+key+'"', {
              fields: 'id,clicks,target,key,admin_key,active'
            });
            
            if (record) {
              // increment the click count
              record.clicks++;
              
              const data = {
                //"is_active": record.is_active,
                "is_active": 1,
                "target": record.target,
                "key": record.key,
                "admin_key": record.admin_key,
                "clicks": record.clicks
              };
              
              const result = await pb.collection('URL').update(record.id, data);
              
              // redirect to the target URL
              window.location.href = record.target;
            }
          } catch (e) {
            console.error(e);
          }
        } else {
          console.log("no key found in URL");
        }


        if (key.includes('admin')) {
          // get the admin key from the URL, issue redirect to the admin page
          let admin_key = key.substring(6);
          console.log(admin_key);
          try {
            const record = await pb.collection('URL').getFirstListItem('admin_key="'+admin_key+'"', {
              fields: 'id,clicks,target,key,admin_key,active'
            });
            
            // TODO - display the admin page using bootstrap modals
            console.log(record);

            // Display the admin page using bootstrap modals
            const modal = document.createElement('div');
            modal.classList.add('modal', 'fade');
            modal.setAttribute('id', 'adminModal');
            modal.setAttribute('tabindex', '-1');
            modal.setAttribute('role', 'dialog');
            modal.setAttribute('aria-labelledby', 'adminModalLabel');
            modal.setAttribute('aria-hidden', 'true');

            const modalDialog = document.createElement('div');
            modalDialog.classList.add('modal-dialog');
            modalDialog.setAttribute('role', 'document');

            const modalContent = document.createElement('div');
            modalContent.classList.add('modal-content');

            const modalHeader = document.createElement('div');
            modalHeader.classList.add('modal-header');

            const modalTitle = document.createElement('h5');
            modalTitle.classList.add('modal-title');
            modalTitle.setAttribute('id', 'adminModalLabel');
            modalTitle.innerText = 'Admin Page';

            const modalBody = document.createElement('div');
            modalBody.classList.add('modal-body');
            //modalBody.innerHTML = 'This is the admin page content.';
            modalBody.innerHTML = '<p>Original URL: <a href="' + record.target + '">' + record.target + '</a></p><p>Shortened URL: <a href="https://vrb.is/'+record.key+'">https://vrb.is/'+record.key+'</a></p><p>Clicks: ' + record.clicks + '</p>';

            const modalFooter = document.createElement('div');
            modalFooter.classList.add('modal-footer');

            // Add a left aligned delete button
            const deleteButton = document.createElement('button');
            deleteButton.classList.add('btn', 'btn-danger');
            deleteButton.setAttribute('type', 'button');
            deleteButton.innerText = 'Delete';
            deleteButton.addEventListener('click', async () => {
              try {
                record.is_active = 0;
                const result = await pb.collection('URL').update(record.id, record);
                console.log(result);
              } catch (e) {
                console.error(e);
              }
            });

            const closeButton = document.createElement('button');
            closeButton.classList.add('btn', 'btn-secondary');
            // ensure button is left aligned:
            closeButton.style.marginLeft = 'auto';
            closeButton.setAttribute('type', 'button');
            closeButton.setAttribute('data-dismiss', 'modal');
            closeButton.innerText = 'Close';
            closeButton.addEventListener('click', () => {
              $('#adminModal').modal('hide');
            });

            modalFooter.appendChild(deleteButton);
            modalFooter.appendChild(closeButton);
            modalHeader.appendChild(modalTitle);
            modalContent.appendChild(modalHeader);
            modalContent.appendChild(modalBody);
            modalContent.appendChild(modalFooter);
            modalDialog.appendChild(modalContent);
            modal.appendChild(modalDialog);
            document.body.appendChild(modal);

            const alert = document.createElement('div');
            alert.classList.add('alert', 'alert-warning');
            alert.setAttribute('role', 'alert');
            alert.innerHTML = 'Shortened URL: <a href="https://vrb.is/'+record.key+'">https://vrb.is/'+record.key+'</a> ( Admin Key for stats/deletion: <a href="https://vrb.is/admin/' + record.admin_key + '">' + record.admin_key + '</a> )';
            document.body.appendChild(alert);

            // Show the admin modal
            $('#adminModal').modal('show');

          } catch (e) {
            console.error(e);
          }
        }
      });
    </script>
  </body>
</html>
