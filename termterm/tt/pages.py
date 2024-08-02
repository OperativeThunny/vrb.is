from flask import Blueprint, render_template
from tt import main

bp = Blueprint("pages", __name__)

@bp.route("/")
def home():
    """ Returns the index page, which displays random adjective-noun pairs.

    Returns:
        str: the html content of the page.
    """
    return main.index()

@bp.route("/about")
def about():
    """ Returns the about page.

    Returns:
        str: the html content of the page.
    """
    return render_template("pages/about.html")
