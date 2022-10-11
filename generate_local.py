import os
import mysql.connector
import time
import random
import datetime  
import pandas as pd
import time

db = mysql.connector.connect(
	host="localhost",
	user="root",
	passwd="admin",
	database="skripsi"
	)

if db.is_connected():
	print("Berhasil terhubung ke database")

	cursor = db.cursor(buffered=True)

	# GET ID SIMULATION
cursor.execute("SELECT id_main_simulation FROM _simulation WHERE status = 1 ORDER BY id DESC LIMIT 1")
id_main_simulation = cursor.fetchone()[0]

begin_generate = time.time()
#Apakah Comparison?
if(id_main_simulation == 0) :
	print("Main simulation")

	# GET ID SIMULATION
	cursor.execute("SELECT id FROM _simulation WHERE status = 1 LIMIT 1")
	id_simulation = cursor.fetchone()[0]

	# GET BATCH NUM
	cursor.execute("SELECT batch_num, id_batch FROM _batch WHERE id_simulation = %s ORDER BY batch_num DESC LIMIT %s",(id_simulation,1))
	if cursor.rowcount > 0:
		batch_num = cursor.fetchone()[0] + 1
	else: 
		batch_num = 1

	# UPDATE CURRENT BATCH
	sql = "UPDATE _batch SET is_current_batch = %s WHERE id_simulation = %s AND is_current_batch = %s"
	val = (0, id_simulation,1)
	cursor.execute(sql, val)
	db.commit()

	# GET TIMESTAMP
	ts = time.time()	
	timestamp = datetime.datetime.fromtimestamp(ts).strftime('%Y-%m-%d %H:%M:%S')

	print(id_simulation)
	print (batch_num)
	print (timestamp)

	# INSERT NEW BATCH
	sql = """INSERT INTO _batch (id_simulation, batch_num, start_time, end_time, is_current_batch) VALUES (%s, %s, %s, null, 1)"""

	val = (id_simulation, batch_num, timestamp)
	cursor.execute(sql,val)
	db.commit()

	# GET ID BATCH NOW
	cursor.execute("SELECT id_batch FROM _batch WHERE id_simulation = %s ORDER BY id_batch DESC LIMIT %s",(id_simulation,1))
	id_batch = cursor.fetchone()[0]

	system = "ON"

	now = datetime.datetime.now()

	if system == "ON":
		todayname = now.strftime("%A")

		daytype = ""

		today7am = now.replace(hour=7, minute=0, second=0, microsecond=0)
		today9am = now.replace(hour=9, minute=0, second=0, microsecond=0)
		today15pm = now.replace(hour=15, minute=0, second=0, microsecond=0)
		today18pm = now.replace(hour=18, minute=0, second=0, microsecond=0)

		min_cust = 0
		max_cust = 0
		min_driv = 0
		max_driv = 0

		jumlah_suburb_c = 0
		jumlah_urban_c = 0
		jumlah_suburb_d = 0
		jumlah_urban_d = 0
		jumlah_passenger = 0
		jumlah_driver = 0

		asal = []
		tujuan = []

		# MENENTUKAN JUMLAH PERMINTAAN
		if todayname == "Saturday" or todayname == "Sunday":
			daytype = "WEEKEND"
			print("WEEKEND")
		else:
			daytype = "WEEKDAY"
			print("WEEKDAY")

		# MENENTUKAN AREA ASAL DAN TUJUAN
		if now>today7am and now<today9am:
			print("PAGI")
			if daytype == "WEEKDAY":
				min_cust = 6
				min_driv = 6
				max_cust = 10
				max_driv = 10
			else:
				min_cust = 2
				min_driv = 2
				max_cust = 5
				max_driv = 5
				
			jumlah_passenger = random.randint(min_cust,max_cust)
			jumlah_driver = random.randint(min_driv,max_driv)	
			
			jumlah_suburb_c = int(0.7*jumlah_passenger)
			jumlah_suburb_d = int(0.7*jumlah_driver)

		elif now>today15pm and now<today18pm:
			print("MALAM")
			if daytype == "WEEKDAY":
				min_cust = 6
				min_driv = 6
				max_cust = 10
				max_driv = 10
					
			else:
				min_cust = 3
				min_driv = 3
				max_cust = 5
				max_driv = 5

			jumlah_passenger = random.randint(min_cust,max_cust)
			jumlah_driver = random.randint(min_driv,max_driv)

			jumlah_suburb_c = int(0.3*jumlah_passenger)
			jumlah_suburb_d = int(0.3*jumlah_driver)

		else:
			print("NORMAL")
			if daytype == "WEEKDAY":
				min_cust = 3
				min_driv = 3
				max_cust = 5
				max_driv = 5			
			else:
				min_cust = 3
				min_driv = 3
				max_cust = 4
				max_driv = 4

			jumlah_passenger = random.randint(min_cust,max_cust)
			jumlah_driver = random.randint(min_driv,max_driv)
				
			jumlah_suburb_c = int(0.5*jumlah_passenger)
			jumlah_suburb_d = int(0.5*jumlah_driver)

		jumlah_passenger = random.randint(min_cust,max_cust)
		jumlah_driver = random.randint(min_driv,max_driv)
		
		# jumlah_passenger = 4	
		# jumlah_driver = 4
		
		jumlah_urban_c = jumlah_passenger-jumlah_suburb_c
		jumlah_urban_d = jumlah_driver-jumlah_suburb_d
			
		# print("Jumlah urban c dan d: "+str(jumlah_urban_c)+" , "+str(jumlah_urban_d))
		# print("Jumlah suburb c dan d: "+str(jumlah_suburb_c)+" , "+str(jumlah_suburb_d))

		print("Jumlah customer: "+str(jumlah_passenger))
		print("Jumlah customer di pinggir: "+str(jumlah_suburb_c))
		print("Jumlah customer di pusat: "+str(jumlah_urban_c))

		print("Jumlah driver: "+str(jumlah_driver))
		print("Jumlah driver di pinggir: "+str(jumlah_suburb_d))
		print("Jumlah driver di pusat: "+str(jumlah_urban_d))

		# GET DRIVER AND PASSENGER AVAILABLE (FROM OFFLINE)
		cursor.execute("SELECT id_passenger FROM _passenger WHERE status = 0 AND is_active = 1")
		passenger_online = cursor.fetchall()
		passenger_online_count = len(passenger_online)

		cursor.execute("SELECT id_driver FROM _driver WHERE status = 0 AND is_active = 1")
		driver_online = cursor.fetchall()
		driver_online_count = len(driver_online)

		print("Jumlah driver online: "+str(driver_online_count))
		print("Jumlah pass online: "+str(passenger_online_count))

		selectedDriver = random.sample(range(0, (driver_online_count)), jumlah_driver)
		selectedPassenger = random.sample(range(0, (passenger_online_count)), jumlah_passenger)

		# print (selectedDriver)
		# print("Selected:")
		# print (selectedPassenger)

		# print(passenger_online)
		# print(driver_online)

		# SAVE ASAL DAN TUJUAN
		cursor.execute("SELECT * FROM _area a JOIN _area_used au ON a.id = au.id_area WHERE a.type = 1 AND au.id_simulation = %s AND a.is_active = %s",(id_simulation,1))
		urban = cursor.fetchall()
		cursor.execute("SELECT * FROM _area a JOIN _area_used au ON a.id = au.id_area WHERE a.type = 2 AND au.id_simulation = %s AND a.is_active = %s",(id_simulation,1))
		suburb = cursor.fetchall()

		# print(len(urban))
		# print(urban)

		# GENERATE PASSENGER DI PUSAT DAN PINGGIR KOTA
		for i in selectedPassenger:
			# MENENTUKAN INDEX AREA YANG MANA
			x = random.randint(0,len(urban)-1)
			y = random.randint(0,len(suburb)-1)
			
			# POSISI AWAL USER
			randomlat = random.uniform(urban[x][2],urban[x][4])
			randomlong = random.uniform(urban[x][3],urban[x][5])

			# POSISI TUJUAN USER
			randomlat2 = random.uniform(suburb[y][2],suburb[y][4])
			randomlong2 = random.uniform(suburb[y][3],suburb[y][5])	

			# INSERT NEW BATCH
			sql = "INSERT INTO _generate_passengers (id_simulation, id_batch, id_passenger, lat_origin, lng_origin, lat_destination, lng_destination, timestamp, status) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)"
			
			# print("Val :")
			if jumlah_urban_c > 0:		
				jumlah_urban_c = jumlah_urban_c - 1
				val = (id_simulation, id_batch, passenger_online[i][0], randomlat,  randomlong, randomlat2, randomlong2, timestamp, 1)
				# print(val);
			elif jumlah_suburb_c > 0:
				jumlah_suburb_c = jumlah_suburb_c - 1
				val = (id_simulation, id_batch, passenger_online[i][0], randomlat2, randomlong2,  randomlat,  randomlong, timestamp, 1)
				# print(val);

			cursor.execute(sql,val)
			db.commit()

			# UPDATE STATUS PASSENGER
			sql = "UPDATE _passenger SET status = %s WHERE id_passenger = %s"
			val = (1, passenger_online[i][0])
			cursor.execute(sql, val)
			db.commit()

		# GENERATE DRIVER DI PUSAT DAN PINGGIR KOTA
		for i in selectedDriver:
			# MENENTUKAN INDEX AREA YANG MANA
			x = random.randint(0,len(urban)-1)
			y = random.randint(0,len(suburb)-1)

			# POSISI URBAN USER
			randomlat = random.uniform(urban[x][2],urban[x][4])
			randomlong = random.uniform(urban[x][3],urban[x][5])

			# POSISI SUBURB USER
			randomlat2 = random.uniform(suburb[y][2],suburb[y][4])
			randomlong2 = random.uniform(suburb[y][3],suburb[y][5])	

			# INSERT NEW BATCH
			sql = "INSERT INTO _generate_drivers (id_simulation, id_batch, id_driver, lat, lng, timestamp, status) VALUES (%s, %s, %s, %s, %s, %s, %s)"
			
			if jumlah_urban_d > 0:		
				jumlah_urban_d = jumlah_urban_d - 1
				val = (id_simulation, id_batch, driver_online[i][0], randomlat,  randomlong, timestamp, 1)
			elif jumlah_suburb_d > 0:
				jumlah_suburb_d = jumlah_suburb_d - 1
				val = (id_simulation, id_batch, driver_online[i][0], randomlat2, randomlong2, timestamp, 1)

			cursor.execute(sql,val)
			db.commit()

			# UPDATE STATUS DRIVER
			sql = "UPDATE _driver SET status = %s WHERE id_driver = %s"
			val = (1, driver_online[i][0])
			cursor.execute(sql, val)
			db.commit()

		timestamp = datetime.datetime.fromtimestamp(ts).strftime('%Y-%m-%d %H:%M:%S')



#Comparison
else :
	print("Comparison simulation")
	# GET ID SIMULATION
	cursor.execute("SELECT id FROM _simulation WHERE status = 1 ORDER BY id DESC LIMIT 1")
	id_simulation = cursor.fetchone()[0]

	# GET BATCH NUM
	cursor.execute("SELECT batch_num, id_batch FROM _batch WHERE id_simulation = %s ORDER BY batch_num DESC LIMIT %s",(id_simulation,1))
	if cursor.rowcount > 0:
		batch_num = cursor.fetchone()[0] + 1
	else: 
		batch_num = 1

	# UPDATE CURRENT BATCH
	sql = "UPDATE _batch SET is_current_batch = %s WHERE id_simulation = %s AND is_current_batch = %s"
	val = (0, id_simulation,1)
	cursor.execute(sql, val)
	db.commit()

	# GET TIMESTAMP
	ts = time.time()	
	timestamp = datetime.datetime.fromtimestamp(ts).strftime('%Y-%m-%d %H:%M:%S')

	print(id_simulation)
	print (batch_num)
	print (timestamp)

	# INSERT NEW BATCH
	sql = "INSERT INTO _batch (id_simulation, batch_num, start_time, end_time, is_current_batch) VALUES (%s, %s, %s, null, 1)"
	val = (id_simulation, batch_num, timestamp)
	cursor.execute(sql,val)
	db.commit()

	# GET ID BATCH NOW
	cursor.execute("SELECT id_batch FROM _batch WHERE id_simulation = %s ORDER BY id_batch DESC LIMIT %s",(id_simulation,1))
	id_batch = cursor.fetchone()[0]
	

	#Read generate sebelum
	selectGeneratePass = pd.read_sql_query('select p.id_simulation, p.id_batch, p.id_passenger, p.lat_origin, p.lng_origin, p.lat_destination, p.lng_destination, p.timestamp, p.status, b.batch_num from _generate_passengers p inner join _batch b on p.id_batch=b.id_batch where batch_num=' + str(batch_num) + ' and p.id_simulation=' + str(id_main_simulation), db)
	selectGenerateDriver = pd.read_sql_query('select d.id_simulation, d.id_batch, d.id_driver, d.lat, d.lng, d.timestamp, d.status, b.batch_num from _generate_drivers d join _batch b on d.id_batch=b.id_batch where batch_num=' + str(batch_num) + ' and d.id_simulation=' + str(id_main_simulation), db)
	
	# Write berdasarkan generate sebelumnya
	# selectGeneratePass = pd.read_sql_query('select p.id_simulation, p.id_batch, p.id_passenger, p.lat_origin, p.lng_origin, p.lat_destination, p.lng_destination, p.timestamp, p.status, b.batch_num from _generate_passengers p inner join _batch b on p.id_batch=b.id_batch where batch_num=1 and p.id_simulation=7', db)
	# df = pd.DataFrame(selectGeneratePass, columns=['id_simulation', 'id_batch', 'id_passenger', 'lat_origin','lng_origin','lat_destination','lng_destination','timestamp', 'status', 'batch_num'])
	passenger = "INSERT INTO _generate_passengers (id_simulation, id_batch, id_passenger, lat_origin, lng_origin, lat_destination, lng_destination, timestamp, status) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)"

	print("Id passenger : ")
	for i in selectGeneratePass.index:
		idp = int(selectGeneratePass['id_passenger'][i])
		lat_origin= float(selectGeneratePass['lat_origin'][i])
		lng_origin= float(selectGeneratePass['lng_origin'][i])
		lat_destination= float(selectGeneratePass['lat_destination'][i])
		lng_destination= float(selectGeneratePass['lng_destination'][i])
		timestamp = datetime.datetime.fromtimestamp(ts).strftime('%Y-%m-%d %H:%M:%S')
		print (idp)
		cursor.execute(passenger, (id_simulation, id_batch, idp, lat_origin, lng_origin, lat_destination, lng_destination, timestamp, 1))
		db.commit()

		# UPDATE STATUS PASSENGER
		sql = "UPDATE _passenger SET status = %s WHERE id_passenger = %s"
		val = (1,int(selectGeneratePass['id_passenger'][i]))
		cursor.execute(sql, val)
		db.commit()

	driver = "INSERT INTO _generate_drivers (id_simulation, id_batch, id_driver, lat, lng, timestamp, status) VALUES (%s, %s, %s, %s, %s, %s, %s)"
	print("Id driver : ")
	
	for i in selectGenerateDriver.index:
		idd = int(selectGenerateDriver['id_driver'][i])
		lat = float(selectGenerateDriver['lat'][i])
		lng = float(selectGenerateDriver['lng'][i])
		timestamp = datetime.datetime.fromtimestamp(ts).strftime('%Y-%m-%d %H:%M:%S')
		print (idd)
		cursor.execute(driver,(id_simulation, id_batch, idd,lat,lng, timestamp, 1))
		db.commit()

		# UPDATE STATUS DRIVER
		sql = "UPDATE _driver SET status = %s WHERE id_driver = %s"
		val = (1, int(selectGenerateDriver['id_driver'][i]))
		cursor.execute(sql, val)
		db.commit()

# # UPDATE DRIVER AND PASS AFTER 3 BATCH
# batch_limit = batch_num_now-3
# cursor.execute("SELECT gd.id_driver FROM _generate_drivers gd JOIN _batch b ON gd.id_batch = b.id_batch WHERE gd.status = %s AND gd.id_simulation = %s AND b.batch_num <= %s",(1,id_simulation,batch_limit))
# res = cursor.fetchall()
# for x in res:
# 	# UPDATE STATUS DRIVER
# 	sql = "UPDATE _driver SET status = %s WHERE id_driver = %s"
# 	val = (0, x[0])
# 	cursor.execute(sql, val)
# 	db.commit()

# 	sql = "UPDATE _generate_drivers SET status = %s WHERE id_driver = %s AND id_simulation = %s"
# 	val = (0, x[0], id_simulation)
# 	cursor.execute(sql, val)
# 	db.commit()

# cursor.execute("SELECT gp.id_passenger FROM _generate_passengers gp JOIN _batch b ON gp.id_batch = b.id_batch WHERE gp.status = %s AND gp.id_simulation = %s AND b.batch_num <= %s",(1,id_simulation,batch_limit))	
# res = cursor.fetchall()
# for x in res:
# 	# UPDATE STATUS PASS
# 	sql = "UPDATE _passenger SET status = %s WHERE id_passenger = %s"
# 	val = (0, x[0])
# 	cursor.execute(sql, val)
# 	db.commit()

# 	sql = "UPDATE _generate_passengers SET status = %s WHERE id_passenger = %s AND id_simulation = %s"
# 	val = (0, x[0], id_simulation)
# 	cursor.execute(sql, val)
# 	db.commit()

# # RANDOM CANCEL
# cursor.execute("SELECT a.id, d.cancellation_rate, gd.id, d.id_driver FROM _assignment a JOIN _generate_drivers gd ON a.id_generate_driver = gd.id JOIN _driver d ON gd.id_driver = d.id_driver WHERE a.status = %s AND a.id_simulation = %s",(1,id_simulation))	
# res = cursor.fetchall()
# for x in res:
# 	reslist=[1,0]

# 	res = numpy.random.choice(reslist, p=[x[1]/100, (100-x[1])/100])
# 	if res == 1:
# 		sql = "UPDATE _assignment SET status = %s WHERE id = %s"
# 		val = (4, x[0])
# 		cursor.execute(sql, val)
# 		db.commit()

# 		# UPDATE STATUS DRIVER
# 		sql = "UPDATE _driver SET status = %s WHERE id_driver = %s"
# 		val = (1, x[3])
# 		cursor.execute(sql, val)
# 		db.commit()

# 		sql = "UPDATE _generate_drivers SET status = %s WHERE id_driver = %s AND id_simulation = %s"
# 		val = (1, x[2], id_simulation)
# 		cursor.execute(sql, val)
# 		db.commit()
			
# UPDATE BATCH END TIME
sql = "UPDATE _batch SET generate_time = %s WHERE id_batch = %s"
val = (time.time() - begin_generate, id_batch)
cursor.execute(sql, val)
db.commit()