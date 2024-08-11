<?php
require_once('includes/http_get.php');

// This is a simple PHP script that redirects the user to the target URL.
if (isset($_REQUEST['q'])) {
  $key = $_REQUEST['q'];
  if (stripos($key, '/admin/') === 0) {
    $admin_key = substr($key, 7);
    // delete the record and add a bootstrap modal to the page via javascript
    // onload saying record deleted
    if (strlen($admin_key) > 12) {
      // this is not a valid admin key
      $admin_key = false;
      exit();
    }
    // issue http get request to the PocketBase API to get the record
    $url = 'https://vrb.is/s/api/collections/URL/records?filter=(admin_key="'.$admin_key.'")';
    $response = http_get($url);
    
  } else {
    $key = str_replace("/", "", $key);
    if (strlen($key) < 5 || strlen($key) > 12) {
      // this is not a valid key
      $key = false;
      exit();
    }
    // get the record and issue redirect and add one to the click count
    // issue http get request to the PocketBase API to get the record
    $url = 'https://vrb.is/s/api/collections/URL/records?filter=(key="'.$key.'")&fields=id,clicks,target,key,admin_key';
    
    $response = http_get($url);
    $responseObj = json_decode($response);
    if (isset($responseObj->items) && count($responseObj->items) > 0) {
      $record = $responseObj->items[0];
      // increment the click count
      $record->clicks++;
      $id = $record->id;
      unset($record->created);
      unset($record->updated);
      unset($record->collectionName);
      unset($record->collectionId);
      unset($record->id);
      // unset($record->target);
      // unset($record->key);
      // unset($record->admin_key);
      // unset($record->active);
      $url = 'https://vrb.is/s/api/collections/URL/records/' . $id;
      //$data = '"data":'.json_encode($record);
      $data = json_encode($record);
      //$data = "clicks=".$record->clicks."";
      printf("<pre>%s</pre>", print_r($data, true));
      $response = http_patch($url, $data, "application/json");
      printf("<pre>%s</pre>", print_r($response, true));
      //header('Location: '.$record->target);
      exit();
    }
  }
}
?>
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
    <script>
      // This is a simple JavaScript function that logs a message to the console.
      function myFunction() {
        console.log("Hello, world!");
      }
      // Register the `myFunction()` function to be executed when the page loads.
      window.addEventListener("load", myFunction);
    </script>
    <script src="/js/bootstrap.min.js"></script>
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
          "active": true,
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
    </script>
  </body>
</html>
