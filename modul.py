from pyomo.environ import *
from pyomo.opt import SolverFactory
import matplotlib.pyplot as plt
import numpy as np
import random
import pymysql
import numpy as np
import pandas as pd
import googlemaps

def append_value(dict_obj, key, value):
    # Check if key exist in dict or not
    if key in dict_obj:
        # Key exist in dict.
        # Check if type of value of key is list or not
        if not isinstance(dict_obj[key], list):
            # If type is not list then make it list
            dict_obj[key] = [dict_obj[key]]
        # Append the value in list
        dict_obj[key].append(value)
    else:
        # As key is not in dict,
        # so, add key-value pair
        dict_obj[key] = value

#Connect Database
hostname = 'tos.petra.ac.id'
username = 'c14180165'
password = 'pemjar'
database = 'c14180165'
dbcon  = pymysql.connect(host=hostname,user=username,password=password,db=database)
cursor = dbcon.cursor()

#Get simulation
try:
    SQL_Query = pd.read_sql_query(
        'select * from _simulation where status=1', dbcon)

    this_simul = pd.DataFrame(SQL_Query, columns=['id', 'simulation_name', 'method', 'start_date','end_date','start_hour','end_hour','status'])
except:
    print("Error: unable to convert the data")

#Get generate driver
SQL_Query = pd.read_sql_query(
    'SELECT * FROM `_generate_drivers` WHERE `id_simulation`=' + this_simul.iloc[0]['id'].astype(str) + ' && status=1 order by timestamp', dbcon)
gen_drivers = pd.DataFrame(SQL_Query, columns=['id', 'id_simulation', 'id_batch', 'id_driver','lat', 'lng','timestamp','status'])

#Get generate passenger
SQL_Query = pd.read_sql_query(
    'SELECT * FROM `_generate_passengers` WHERE `id_simulation`=' + this_simul.iloc[0]['id'].astype(str) + ' && status=1 order by timestamp', dbcon)
gen_passengers = pd.DataFrame(SQL_Query, columns=['id', 'id_simulation', 'id_batch', 'id_passenger','lat_origin', 'lng_origin','lat_destination','lng_destination','timestamp','status'])

#Count Total Distance
API_key = 'AIzaSyD9bMNF3l5Inp_3DPs4x1lxkpuSm_-WeP8' 
gmaps = googlemaps.Client(key=API_key)
destinations = []

list_distance = {}
list_duration = {}
case_list = {}
for i in gen_drivers.index:
    case_list[gen_passengers['id'][i]] = {}
    list_distance[gen_passengers['id'][i]] = {}
    list_duration[gen_passengers['id'][i]] = {}
    
    for j in gen_passengers.index:
        matrix = gmaps.distance_matrix([str(gen_drivers['lat'][j]) + ' ' + str(gen_drivers['lng'][j])], [str(gen_passengers['lat_origin'][i]) + ' ' + str(gen_passengers['lng_origin'][i])], mode="driving")['rows'][0]['elements'][0]
#         print(matrix)
        list_distance[gen_passengers['id'][i]][gen_drivers['id'][j]] = matrix['distance']['text']
        list_duration[gen_passengers['id'][i]][gen_drivers['id'][j]] = matrix['duration']['text']
        case_list[gen_passengers['id'][i]][gen_drivers['id'][j]] = int(matrix['duration']['value'])        

print("Case list: ")
print(case_list)

# Create an object to perform optimization
opt = SolverFactory('cplex')

# Create an object of a concrete model
model = ConcreteModel()

# Define the decision variables
arr = {}
d = {}
count = 0

for i in case_list:
	for j in case_list[i]:
		d[i,j] = case_list[i][j]
		count=count+1

print("D: ")
print(d)

N = 2
M = 2
model.Drivers = range(N)
model.Customers = range(M)

model.x = Var( model.Drivers, model.Customers, bounds=(0.0,1.0) )
model.y = Var( model.Drivers, within=Binary )

model.obj = Objective( expr = sum( d[n,m]*model.x[n,m] for n in model.Drivers for m in model.Customers ) )

model.single_x = ConstraintList()

for m in model.Customers:
    model.single_x.add(sum( model.x[n,m] for n in model.Drivers ) == 1.0 )

model.bound_y = ConstraintList()

for n in model.Drivers:
    for m in model.Customers:
        model.bound_y.add( model.x[n,m] <= model.y[n] )

model.num_facilities = Constraint(expr=sum( model.y[n] for n in model.Drivers ) == 2 )

results = opt.solve(model)

print(model.x)
print(model.y)