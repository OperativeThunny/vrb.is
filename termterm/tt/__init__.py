from flask import Flask, url_for

from tt import pages

def create_app():
    app = Flask(__name__)
    app.register_blueprint(pages.bp)
    return app

application = create_app()

@application.route("/")
def index():
    return url_for("pages.home")
