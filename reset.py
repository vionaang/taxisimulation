import os
import mysql.connector
import time
import random
import datetime  
import pandas as pd
import time

db = mysql.connector.connect(
    host="tos.petra.ac.id",
    user="c14180165",
    passwd="pemjar",
    database="c14180165"
    )

cursor = db.cursor()

cursor.execute("UPDATE _driver SET status = 0");
db.commit()

cursor.execute("UPDATE _passenger SET status = 0");
db.commit()

cursor.execute("UPDATE _generate_drivers SET status = 0");
db.commit()

cursor.execute("UPDATE _generate_passengers SET status = 0");
db.commit()

cursor.execute("UPDATE _assignment SET status = 3");
db.commit()