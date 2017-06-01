#Refactoring options for stats
We need to do it, here are some ideas.  

##1. Bandaid
In this solution, a new table, `feedback_rounds` is added and is used to track a variety of 'common' data about rounds, such as:  

* `round_id`
* `game_mode`
* `round_end_result`
* `end_error` <-- If any error condition was thrown (I.E. NOT NUKE)
* `survivors`
* `escapees`
* `dead`
* `station_integrity`
* `round_start`
* `round_end`
* `duration`
* Along with various other datapoints (ops declared war, what shuttle was purchased/evacuated on, alert levels, etc)

The `feedback_feedback` table is still used, but only for big chunks of data (`ores_mined`, `traitor_objectives`, etc)

The difficulty with this is allocating a `round_id`, but that should be a relatively simple matter of executing a DB query when the game loads. On the other hand, the `round_id` being allocated like this means it's available to other data as well, like bans, deaths, and connection logging.

##2. W E B S C A L E
The existing feedback is smushed into a giant JSON string and shoved into a MongoDB instance. 