[![Build Status](https://travis-ci.org/igorbelikov/9-balls-and-scales.svg?branch=master)](https://travis-ci.org/igorbelikov/9-balls-and-scales)

# 9 balls and scales
You have 9 balls, equally big, equally heavy - except for one, which is a little heavier.
How would you identify the heavier ball if you could use a pair of balance scales only twice?

DEMO:
http://dev.ibelik.com/9balls/

Screenshot:
![alt tag](http://dev.ibelik.com/9balls/screenshoot.png)

Screenshot, scheme and dump located in the `data` directory.

Install:

1. `composer install`

2. `bower install`

3. `vendor/bin/phinx init`

5. edit phinx.yml config

4. `vendor/bin/phinx migrate`

Test:

`php vendor/phpunit/phpunit/phpunit tests/BallsTest`

