# What is this?
Hey guys! I just received a challenge from Agency Analytics.
This is my solution! In order to get this challenge going, I created a docker box.
Been using it for a while and really like it's simplicity.
For this challenge, I decided to create a bootstrap frontend website where the user can submit a URL and a number of pages to crawl.
the request and save data into db.
I am using a Laravel 9 framework for this project on a PHP 8+ nginx server.
Please refer yourself to this readme for a full explanation on how it works.

## About This challenge

Using PHP, build a web crawler to display information about a given website.
Crawl 4-6 pages of our website agencyanalytics.com given a single entry point. Once
the crawl is complete, display the following results:

- Number of pages crawled
- Number of a unique images
- Number of unique internal links
- Number of unique external links
- Average page load in seconds
- Average word count
- Average title length

Also display a table that shows each page you crawled and the HTTP status code
Deploy to a server that can be accessed over the internet. You can use any host of
your choosing such as AWS, Google Cloud, Heroku, Azure etc. Be sure to include the
url in your submission.

## Docker Information

I created a very nice docker setup for this challenge. It's composed of 3 containers.
    `web_crawler_app`
    `web_crawler_webserver`
    `web_crawler_db`
## Setup instructions

Prerequisite:
Make sure below tool should be on your local.

    composer 1/2
    Php 8+

Repository and Docker setup

    mkdir web_crawler
    cd web_crawler
    git@github.com:hammadtahirch/web-crawler.git
    composer install
    docker-compose up -d --build

Enter the PHP FPM container and type the following

    chmod -R 0777 storage
    mv "example.env" ".env"

Connected with following container ``web_crawler_app``
and run migrations

    php artisan migrate

You should now have access to http://localhost:2000

## Project highlights

- Added docker to the project with a 3 containers network setup (PHP8 + nginx)
- Added bootstrap to the project for the user frontend
- Created nice clean code
- Created a nice readme file
- Explained how to reproduce the solution and how it works

