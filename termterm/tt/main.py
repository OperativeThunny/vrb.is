"""
This module implements a simple Flask web application that generates random 
combinations of adjectives and nouns.

The application reads adjectives and nouns from text files, stores them in 
global variables, and provides an endpoint to display a specified number of 
random adjective-noun pairs.

Functions:
    load(): Loads adjectives and nouns from text files into global variables.
    random_adjective(): Selects a random adjective from the list of adjectives.
    random_noun(): Selects a random noun from the list of nouns.
    index(): Flask route that generates and returns an HTML page with random 
             adjective-noun pairs.

Usage:
    Run this module directly to start the Flask web server:
        python main.py
"""
import secrets

from flask import Flask, request, render_template
#from fastapi import FastAPI
#import random # mersenne twister
import numpy as np

loaded = False
adjectives = []
nouns = []

# Get a cryptographically secure PRNG
random = secrets.SystemRandom()
random = np.random.default_rng(secrets.randbits(128))

def load():
    """
    Loads adjectives and nouns from text files into global variables.

    This function reads the contents of `english-adjectives.txt` and 
    `english-nouns.txt` files located in the `./tt/` directory, processes 
    the contents to remove newline characters, and stores them in the 
    global variables `adjectives` and `nouns`. It also sets the global 
    `loaded` flag to True if the files are successfully loaded.

    Returns:
        bool: True if the files are successfully loaded, otherwise False.
    """
    global loaded, adjectives, nouns

    print("load called")
    if loaded:
        return True
    adjectives_file = open("./tt/english-adjectives.txt", "r", encoding="utf-8")
    nouns_file = open("./tt/english-nouns.txt", "r", encoding="utf-8")
    try:
        adjectives = list(adjectives_file)
        nouns = list(nouns_file)
        for i, s in enumerate(adjectives):
            adjectives[i] = s.strip()
        for i, s in enumerate(nouns):
            nouns[i] = s.strip()
        loaded = True
        return True
    finally:
        adjectives_file.close()
        nouns_file.close()
    return False

def random_adjective():
    """
    Selects a random adjective from the list of adjectives.

    Returns:
        str: A randomly selected adjective.
    """
    return random.choice(adjectives)

def random_noun():
    """
    Selects a random noun from the list of nouns.

    Returns:
        str: A randomly selected noun.
    """
    return random.choice(nouns)

def index():
    """
    Flask route that generates and returns an HTML page with random 
    adjective-noun pairs.

    This function checks if the adjectives and nouns have been loaded into 
    global variables. If not, it loads them. It then generates a specified 
    number of random adjective-noun pairs and constructs an HTML page to 
    display them. The number of pairs can be specified via a query parameter 
    'num_terms', with a default of 30 and a maximum of 1000.

    Returns:
        str: An HTML string containing the generated page.
    """
    global loaded
    if not loaded:
        loaded = load()

    num_terms = 30
    if 'num_terms' in request.args:
        num_terms = abs(int(request.args['num_terms'])) # abs() to prevent negative numbers
    num_terms = min(num_terms, 1000) # limit to 1000 terms

    termterms = []

    for _ in range(num_terms):
        adjective = random_adjective()
        noun = random_noun()
        termterms.append([ adjective, noun ])
    
    return render_template("pages/tt.html", termterms=termterms)

