# Bike-sharing application
One key area that NordSecurity as a company is actively paying attention to is employee's [physical well-being](https://nordsecurity.com/blog/in-house-training-keeps-our-team-motivated)!
So we here at NordLocker have written a script that helps people find the closest bike-sharing station from any location. You are given a script that should:
* fetch and process data from [CityBike API](http://api.citybik.es/v2/) for any city that you choose (maybe it can be yours if possible!);
* parse a CSV file in which we have bikers in random locations. An example file (bikers.csv) is attached;
* calculate the closest bike station and print it out to screen.  

However, the script is poorly written and you will need to fix it so that it:
* Fetches and processes the following data:
  * Stations names
  * Stations coordinates (latitude and longitude)
  * The number of free bikes available at the stations
* Parses a CSV file that contains:
  * The count of bikers in need of a bike;
  * The coordinates (latitude and longitude) at which the bikes are located.  Example file structure:
```
count, latitude, longitude
2, 45.69233, 9.65931
1, 45.69654, 9.65897
0, 45.67831, 9.67516
4, 45.716909, 9.716649
```
* This example displays the closest bike station and the bikes available in Bergamo, Italy:
```
php script.php Bergamo

distance: 0.024340748060035
name: 19. Palma il Vecchio
free_bike_count: 0
biker_count: 2

distance: 0.096520693247684
name: 23. Mazzini
free_bike_count: 0
biker_count: 1

distance: 0.36324024061141
name: VIA AMBIVERI
free_bike_count: 0
biker_count: 0

distance: 1.2777018248149
name: VIA CORRIDONI - MARTINELLA
free_bike_count: 0
biker_count: 4
```
The task:
* Refactor the script to fit OOP (Object-Oriented Programming) principles;
* Write tests for bike station parsing and distance calculation. Don’t write additional tests — more tests won’t count as a bonus and you will save time. :)  

Things to look out for when refactoring (hard requirements):
* Validation of incoming data — errors should be printed to screen instead of fatal PHP errors;
* If you store the task on a public domain, don’t mention any NordSecurity trademarks (e.g. NordLocker, NordVPN, NordPass) in the task. Doing so can negatively affect Google search results;
* PSR Coding standards;
* The task should be implemented with PHP 8.0 or higher;
* Ability to reuse/replace data with ease. For example, changing the bike group format from CSV to XML;
* Double-check for any business logic or implementation flaws.  

Optional things to consider (soft requirements that you should do only if you have time left):
* Processing of large files. What if the system was intended to serve millions of biker groups looking for a bike globally?
* Containerization. For example, a docker-compose configuration that would create a container.

We estimate that implementing the hard requirements should take you about 4-6 hours.  
If it takes you more time to complete the tasks, please share your feedback with the person who assigned you this task — it helps us improve.
