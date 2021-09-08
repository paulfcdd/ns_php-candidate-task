# Cycling in Copenhagen
Copenhagen is known for being a [cyclist city](https://www.weforum.org/agenda/2018/10/what-makes-copenhagen-the-worlds-most-bike-friendly-city/)!
And, since Copenhagen has a great bike infrastructure, we have written a script that helps people find the closest bike-sharing station from any location.You are given a script that should:
* fetch and process data from [CityBike API](http://api.citybik.es/v2/) for Copenhagen city;
* parse a CSV file in which we have bikers in random locations. An example file (bikers.csv) is attached;
* calculate the closest bike station and print it out to screen.  

However, the script is poorly written and you’ll need to fix it so that it:
* Fetches and processes the following data:
  * Station address
  * Station coordinates (latitude and longitude)
  * The number of free bikes available at the station
* Parses a CSV file that contains:
  * The count of bikers in need of a bike;
  * The coordinates (latitude and longitude) at which the bikes are located.  Example file structure:
```
count, latitude, longitude
2, 55.67766, 12.59747
1, 55.68186, 12.56989
0, 55.64675, 12.54854
4, 55.70909881591797, 12.603099822998047
```
* Displays the closest bike station and the bikes available there, like this:
```
distance: 0.00021154609843014
address: Strandgade 108, København, 1401 Copenhagen
free_bike_count: 1
biker_count: 2
distance: 0.0012151525633947
address: Nørre Voldgade 48, København (DSB), 1358 København K
free_bike_count: 14
biker_count: 1
distance: 0.00062542893520177
address: Thad Jones Vej 4, København, 2450 København SV
free_bike_count: 16
biker_count: 0
distance: 0
address: Klubiensvej 22, København, Copenhagen
free_bike_count: 4
biker_count: 4
```
The task:
* Refactor the script to fit OOP (Object-Oriented Programming) principles;
* Write tests for bike station parsing and distance calculation. Don’t write additional tests — more tests won’t count as a bonus and you will save time. :)  

Things to look out for when refactoring (hard requirements):
* Validation of incoming data — errors should be printed to screen instead of fatal PHP errors;
* If you store the task on a public domain, don’t mention any NordSecurity trademarks (e.g. NordLocker, NordVPN, NordPass) in the task. Doing so can negatively affect Google search results;
* PSR Coding standards;
* The task should be implemented with PHP 7.4 or higher;
* Ability to reuse/replace data with ease. For example, changing the bike group format from CSV to XML;
* Double-check for any business logic or implementation flaws.  

Optional things to consider (soft requirements that you should do only if you have time left):
* Processing of large files. What if the system was intended to serve millions of biker groups looking for a bike globally?
* Containerization. For example, a docker-compose configuration that would create a container.

We estimate that implementing the hard requirements should take you about 4-6 hours.  
If it takes you more time to complete the tasks, please share your feedback with the person who assigned you this task — it helps us improve.
