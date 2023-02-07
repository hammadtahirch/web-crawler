# What is this?
I recently received a challenge from Agency Analytics and have created a solution. To start the challenge, I utilized a Docker container for its simplicity. For this specific challenge, I built a Bootstrap frontend website where the user can submit a URL and specify the number of pages to crawl. The website will then process the request and store the data in a database. I am using the Laravel 9 framework and a PHP 8+ nginx server for this project. For a detailed explanation of how the solution works, please refer to the accompanying readme file.

## About This challenge

Using PHP, build a web crawler to display information about a given website.
Crawl 4-6 pages of our website agencyanalytics.com given a single entry point. Once
the crawl is complete, display the following results:

- Number of pages crawled
- Number of unique images
- Number of unique internal links
- Number of unique external links
- Average page load in seconds
- Average word count
- Average title length

Also, display a table that shows each page you crawled and the HTTP status code
Deploy to a server that can be accessed over the internet. You can use any host of
your choosing such as AWS, Google Cloud, Heroku, Azure etc. Be sure to include the
URL in your submission.

## Docker Information

I created a very nice docker setup for this challenge. It's composed of 3 containers.
`web_crawler_app`
`web_crawler_webserver`
`web_crawler_db`
## Setup instructions

Prerequisite:
Make sure the below tool should be on your local.

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

Connect with the following container ``web_crawler_app``

    docker exec -it {conatiner_id} bash
To run the ``migration`` type following.

    php artisan migrate

You should now have access to http://localhost:2000

## Run Tests
Connect with following container ``web_crawler_app`` type following

    docker exec -it {conatiner_id} bash

After connecting to ``container`` please type the following.

    ./vendor/bin/phpunit tests/

``Note:`` all test reside inside ``tests/Feature``


## Project highlights

- Added docker to the project with a 3 containers network setup (PHP8 + nginx)
- Added bootstrap to the project for the user frontend
- Created nice clean code
- Added some feature test
- Created a nice readme file
- Explained how to reproduce the solution and how it works

## There are areas in the project that could be improved.


- It is not a good idea to use page crawling for more than 10,20 pages. Instead, it is better to use the crawlerServicer wrapped in a Laravel queue. Break the pages into small chunks, such as 2 or 3 pages per queue call.
- The average data is not entirely accurate and may require additional review to improve its precision.
- I chose to use a database session for this project as it was relatively small, and I do not have confidence in file sessions. It would have been more suitable to utilize Redis for session management.
- The database structure could be improved by using a properly normalized table structure.
- The code quality can also be improved by implementing more unit testing.

## I always consider myself a student, and I welcome constructive criticism, suggestions, and feedback.
