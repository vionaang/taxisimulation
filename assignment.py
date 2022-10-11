from __future__ import print_function
import os
import numpy as np
import pandas as pd
import googlemaps
import datetime
import time
import pymysql
from dateutil.relativedelta import relativedelta
from sklearn import preprocessing
from scipy.optimize import linear_sum_assignment
import mysql.connector
import random

begin = time.time()

db = mysql.connector.connect(
	host="tos.petra.ac.id",
	user="c14180165",
	passwd="pemjar",
	database="c14180165"
	)
if db.is_connected():
	print("Berhasil terhubung ke database")

	cursor = db.cursor(buffered=True)

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
sql = """INSERT INTO _batch (id_simulation, batch_num, start_time, end_time, run_time, is_current_batch) VALUES (%s, %s, %s,null,null,1)"""
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
			min_cust = 2
			min_driv = 2
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
			min_cust = 1
			min_driv = 1
			max_cust = 3
			max_driv = 3

		jumlah_passenger = random.randint(min_cust,max_cust)
		jumlah_driver = random.randint(min_driv,max_driv)

	# jumlah_passenger = 5;
	# jumlah_driver = 5;

		jumlah_suburb_c = int(0.5*jumlah_passenger)
		jumlah_suburb_d = int(0.5*jumlah_driver)

		jumlah_urban_c = jumlah_passenger-jumlah_suburb_c
		jumlah_urban_d = jumlah_driver-jumlah_suburb_d

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

	print (selectedDriver)
	print (selectedPassenger)

	# print(passenger_online)
	# print(driver_online)

	# SAVE ASAL DAN TUJUAN
	cursor.execute("SELECT * FROM _area a JOIN _area_used au ON a.id = au.id_area WHERE a.type = 1 AND au.id_simulation = %s AND a.is_active = %s",(id_simulation,1))
	urban = cursor.fetchall()
	cursor.execute("SELECT * FROM _area a JOIN _area_used au ON a.id = au.id_area WHERE a.type = 2 AND au.id_simulation = %s AND a.is_active = %s",(id_simulation,1))
	suburb = cursor.fetchall()

	# print(len(urban))
	# print(len(suburb))

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
		
		if jumlah_urban_c > 0:		
			jumlah_urban_c = jumlah_urban_c - 1
			val = (id_simulation, id_batch, passenger_online[i][0], randomlat,  randomlong, randomlat2, randomlong2, timestamp, 1)
		elif jumlah_suburb_c > 0:
			jumlah_suburb_c = jumlah_suburb_c - 1
			val = (id_simulation, id_batch, passenger_online[i][0], randomlat2, randomlong2,  randomlat,  randomlong, timestamp, 1)

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

	# UPDATE BATCH END TIME
	sql = "UPDATE _batch SET end_time = %s WHERE id_batch = %s"
	val = (timestamp, id_batch)
	cursor.execute(sql, val)
	db.commit()

from datetime import datetime
from datetime import timedelta

#Connect Database
hostname = 'tos.petra.ac.id'
username = 'c14180165'
password = 'pemjar'
database = 'c14180165'
dbcon  = pymysql.connect(host=hostname,user=username,password=password,db=database)
cursor = dbcon.cursor()

#Read data simulasi
this_simul = pd.read_sql_query(
		    'select * from _simulation where status=1', dbcon)

#Read data factor_used
SQL_Query = pd.read_sql_query(
    'SELECT id, id_factor, id_simulation, precentage FROM `_factor_used` WHERE `id_simulation`=' + this_simul.iloc[0]['id'].astype(str) + ' order by id_factor', dbcon)

factor_used = pd.DataFrame(SQL_Query, columns=['id', 'id_factor', 'id_simulation', 'precentage'])

#Read data factor
factor = pd.read_sql_query(
    'SELECT id, name, goal, user_type FROM `_factor` order by id', dbcon)

factor_used = pd.merge(factor_used,factor, left_on='id_factor', right_on='id')
factor_used = factor_used.drop(columns="id_y")
factor_used = factor_used.rename(columns={'id_x': 'id'})
factor_used

#Read data generate driver
SQL_Query = pd.read_sql_query(
    'SELECT * FROM `_generate_drivers` WHERE `id_simulation`=' + this_simul.iloc[0]['id'].astype(str) + ' && status=1 order by id', dbcon)

ori_gen_drivers = pd.DataFrame(SQL_Query, columns=['id', 'id_simulation', 'id_batch', 'id_driver','lat', 'lng','timestamp','status'])

#Read data driver
SQL_Query = pd.read_sql_query(
        'select * from _driver', dbcon)

driver = pd.DataFrame(SQL_Query, columns=['id_driver', 'name', 'rating', 'total_trip','cancellation_rate','total_distance','status','is_active', 'rfm_score_driver'])

driver = driver.drop(columns="status")
driver

#Read data generate passenger
SQL_Query = pd.read_sql_query(
    'SELECT * FROM `_generate_passengers` WHERE `id_simulation`=' + this_simul.iloc[0]['id'].astype(str) + ' && status=1 order by timestamp', dbcon)

ori_gen_passengers = pd.DataFrame(SQL_Query, columns=['id', 'id_simulation', 'id_batch', 'id_passenger','lat_origin', 'lng_origin','lat_destination','lng_destination','timestamp','status'])

#Read data passenger
SQL_Query = pd.read_sql_query(
        'select id_passenger, name, rfm_score_pass, is_active from _passenger', dbcon)

passenger = pd.DataFrame(SQL_Query, columns=['id_passenger', 'name', 'rfm_score_pass','is_active'])

#Menggabungkan data generate passenger dan passenger
ori_gen_passengers = ori_gen_passengers.join(passenger.set_index('id_passenger'), on='id_passenger')
ori_gen_passengers

#Menggabungkan data generate driver dan driver
ori_gen_drivers = ori_gen_drivers.join(driver.set_index('id_driver'), on='id_driver')
ori_gen_drivers

gen_drivers = ori_gen_drivers.copy()
gen_passengers = ori_gen_passengers.copy()

#Fungsi normalisasi
def normalisasi(df,col_name, goal):
    min = df[col_name].min()
    max = df[col_name].max()
    df[col_name] = df[col_name].astype(float)
    
    for i in df.index:
        normalisasi = float((df[col_name][i]-min)/(max-min))
        if(goal=="max"):
            df[col_name][i] = 1-normalisasi
        else:
            df[col_name][i] = normalisasi

#mengecek penggunaan faktor
cekrating = False
cektotal_trip = False
cekcancel = False
cektotaldis = False
cekrfmp = False
cekrfmd = False
cekdur = False
cekdis = False
count = 0

for i in factor_used.index:
    if(factor_used['user_type'][i]=="driver"):
        
        normalisasi(gen_drivers, factor_used['name'][i],factor_used['goal'][i])
        if(factor_used['name'][i]=="rating"):
            cekrating = True
            count+=1
        elif(factor_used['name'][i]=="total_trip"):
            cektotal_trip = True
            count+=1
        elif(factor_used['name'][i]=="cancellation_rate"):
            cekcancel = True
            count+=1
        elif(factor_used['name'][i]=="total_distance"):
            cektotaldis = True
            count+=1
        elif(factor_used['name'][i]=="rfm_score_driver"):
            cekrfmd = True
            count+=1
            
    elif(factor_used['user_type'][i]=="passenger"):
        normalisasi(gen_passengers, factor_used['name'][i],factor_used['goal'][i])
        if(factor_used['name'][i]=="rfm_score_pass"):
            cekrfmp = True
            count+=1
    elif(factor_used['name'][i]=="distance"):
        cekdis = True
        count+=1
    elif(factor_used['name'][i]=="duration"):
        cekdur = True
        count+=1

if(cekrfmp==True):
	this_batch = pd.read_sql_query(
	'SELECT batch_num, id_batch FROM _batch WHERE id_simulation ='+ this_simul.iloc[0]['id'].astype(str) +' ORDER BY batch_num DESC Limit 1', dbcon)

	#Mengambil data factor_used
	factor = pd.read_sql_query(
	'select * from _factor_used where `id_simulation`=' + this_simul.iloc[0]['id'].astype(str) + '&& id_factor=5', dbcon)

	data_cust = pd.read_sql_query(
	'select * from _generate_passengers where id_simulation=' + this_simul.iloc[0]['id'].astype(str) + "&& id_batch=" + this_batch.iloc[0]['id_batch'].astype(str), dbcon)

	string = ", ".join(data_cust.id_passenger.astype(str).tolist())
	print("Id passenger RFM : " + string)

	data_transaksi = pd.read_sql_query(
	    'select * from _transaksi', dbcon)

	#Hitung recency
	data_recency = data_transaksi.groupby(by='id_passenger', as_index=False)['transaction_date'].max()
	data_recency.columns = ['id_passenger','LastPurchaseDate']

	now=datetime.now()
	data_recency['Recency']=""
	for i in data_recency.index:
	    data_recency['Recency'][i] = (now-data_recency['LastPurchaseDate'][i]).days

	#jika RFM dimodifikasi
	now = datetime.now()
	if(this_simul.iloc[0]['rfm_filter'] != 0) :
	    #Menghitung tanggal berapa h-hari inputan
	    day = int(this_simul.iloc[0]['rfm_filter'])
	    tanggal = now - timedelta(days=day)
	    print(tanggal.strftime('%Y-%m-%d'))
	    data_transaksi = data_transaksi.query("transaction_date >= @tanggal.strftime('%Y-%m-%d')")
	    data_transaksi.sort_values(by=['transaction_date'])

	 #Hitung frekuensi
	data_frequency = data_transaksi.groupby(by=['id_passenger'], as_index=False)['transaction_date'].count()
	data_frequency.columns = ['id_passenger','Frequency']

	#hitung monetary
	data_monetary = data_transaksi.groupby(by='id_passenger',as_index=False)['price'].sum()
	data_monetary.columns = ['id_passenger','Monetary']

	#membuat pembagian recency
	tabel_rfm = data_recency.merge(data_frequency,on='id_passenger')
	tabel_rfm = tabel_rfm.merge(data_monetary,on='id_passenger')

	tabel_rfm['R_Score'] = np.where(tabel_rfm['Recency'] <= factor.iloc[0]['recency1'], 5, 
	                            np.where(tabel_rfm['Recency'] <= factor.iloc[0]['recency2'], 4, 
	                                     np.where(tabel_rfm['Recency'] <= factor.iloc[0]['recency3'], 3,
	                                             np.where(tabel_rfm['Recency'] <= factor.iloc[0]['recency4'], 2,
	                                                      np.where(tabel_rfm['Recency'] <= factor.iloc[0]['recency5'], 1, 0)))))

	#Membuat pembagian quantiles frekuensi dan monetary
	quantiles = tabel_rfm.quantile(q=[0.2,0.4,0.6, 0.8])
	quantiles.to_dict()	

	def FMScore(x,p,d):
		if x <= d[p][0.2]:
		    return 1
		elif x <= d[p][0.4]:
		    return 2
		elif x <= d[p][0.6]: 
		    return 3
		elif x <= d[p][0.8]: 
		    return 4
		else:
		    return 5

	tabel_rfm['F_Score'] = tabel_rfm['Frequency'].apply(FMScore, args=('Frequency',quantiles,))
	tabel_rfm['M_Score'] = tabel_rfm['Monetary'].apply(FMScore, args=('Monetary',quantiles,))

	tabel_rfm['RFM_Segment'] = tabel_rfm.R_Score.map(str) \
	                        + tabel_rfm.F_Score.map(str) \
	                        + tabel_rfm.M_Score.map(str)

	tabel_rfm['RFM_Score'] = tabel_rfm['R_Score'] + tabel_rfm['F_Score'] + tabel_rfm['M_Score']
	tabel_rfm = tabel_rfm.sort_values(by=['RFM_Score'], ascending=False)                        

	tabel_jadi = data_cust.join(tabel_rfm.set_index('id_passenger'), on='id_passenger')
	tabel_jadi = tabel_jadi.fillna(1)

	#memasukkan ke dalam database
	query = "UPDATE `_passenger` SET `rfm_score_pass`=%s WHERE `id_passenger`=%s"
	query_rfm = "INSERT INTO `_rfm_data`(`id_simulation`, `id_batch`, `id_pass`, `recency`, `frequency`, `monetary`, `r_score`, `f_score`, `m_score`, `rfm_score`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" 

	for i in tabel_jadi.index:
	    cursor.execute(query, (tabel_jadi['RFM_Score'][i], tabel_jadi['id_passenger'][i]))
	    cursor.execute(query_rfm, (this_simul.iloc[0]['id'].astype(str), this_batch.iloc[0]['id_batch'].astype(str), tabel_jadi['id_passenger'][i], tabel_jadi['Recency'][i], tabel_jadi['Frequency'][i], tabel_jadi['Monetary'][i], tabel_jadi['R_Score'][i], tabel_jadi['F_Score'][i], tabel_jadi['M_Score'][i], tabel_jadi['RFM_Score'][i]))
	    dbcon.commit()

	print("RFM Passenger sukses")

if(cekrfmd == True):
	this_batch = pd.read_sql_query(
	    'SELECT batch_num, id_batch FROM _batch WHERE id_simulation ='+ this_simul.iloc[0]['id'].astype(str) +' ORDER BY batch_num DESC Limit 1', dbcon)

	#Mengambil data factor_used
	factor = pd.read_sql_query(
	    'select * from _factor_used where `id_simulation`=' + this_simul.iloc[0]['id'].astype(str) + '&& id_factor=5', dbcon)

	data_driver = pd.read_sql_query(
	    'select * from _generate_drivers where id_simulation=' + this_simul.iloc[0]['id'].astype(str) + "&& id_batch=" + this_batch.iloc[0]['id_batch'].astype(str), dbcon)

	string = ", ".join(data_driver.id_driver.astype(str).tolist())
	print("Id driver RFM : " + string)
	
	data_transaksi = pd.read_sql_query(
	    'select * from _transaksi', dbcon)

	#Hitung recency
	data_recency = data_transaksi.groupby(by='id_driver', as_index=False)['transaction_date'].max()
	data_recency.columns = ['id_driver','LastJobDate']

	now=datetime.now()
	data_recency['Recency']=""
	for i in data_recency.index:
	    data_recency['Recency'][i] = (now-data_recency['LastJobDate'][i]).days

	data_recency

	#jika RFM dimodifikasi
	now = datetime.now()
	if(this_simul.iloc[0]['rfm_filter'] != 0) :
	    #Menghitung tanggal berapa h-hari inputan
	    day = int(this_simul.iloc[0]['rfm_filter'])
	    tanggal = now - timedelta(days=day)
	    print(tanggal.strftime('%Y-%m-%d'))
	    data_transaksi = data_transaksi.query("transaction_date >= @tanggal.strftime('%Y-%m-%d')")
	    data_transaksi.sort_values(by=['transaction_date'])

	 #Hitung frekuensi
	data_frequency = data_transaksi.groupby(by=['id_driver'], as_index=False)['transaction_date'].count()
	data_frequency.columns = ['id_driver','Frequency']

	#hitung monetary
	data_monetary = data_transaksi.groupby(by='id_driver',as_index=False)['price'].sum()
	data_monetary.columns = ['id_driver','Monetary']

	#membuat pembagian recency
	tabel_rfm = data_recency.merge(data_frequency,on='id_driver')
	tabel_rfm = tabel_rfm.merge(data_monetary,on='id_driver')
	tabel_rfm.head(5)

	tabel_rfm['R_Score'] = np.where(tabel_rfm['Recency'] <= factor.iloc[0]['recency1'], 5, 
	                                np.where(tabel_rfm['Recency'] <= factor.iloc[0]['recency2'], 4, 
	                                         np.where(tabel_rfm['Recency'] <= factor.iloc[0]['recency3'], 3,
	                                                 np.where(tabel_rfm['Recency'] <= factor.iloc[0]['recency4'], 2,
	                                                          np.where(tabel_rfm['Recency'] <= factor.iloc[0]['recency5'], 1, 0)))))

	#Membuat pembagian freq dan monetary
	quantiles = tabel_rfm.quantile(q=[0.2,0.4,0.6, 0.8])

	quantiles.to_dict()

	def FMScore(x,p,d):
	    if x <= d[p][0.2]:
	        return 1
	    elif x <= d[p][0.4]:
	        return 2
	    elif x <= d[p][0.6]: 
	        return 3
	    elif x <= d[p][0.8]: 
	        return 4
	    else:
	        return 5

	#Membuat tabel RFM
	tabel_rfm['F_Score'] = tabel_rfm['Frequency'].apply(FMScore, args=('Frequency',quantiles,))
	tabel_rfm['M_Score'] = tabel_rfm['Monetary'].apply(FMScore, args=('Monetary',quantiles,))

	tabel_rfm['RFM_Score'] = tabel_rfm['R_Score'] + tabel_rfm['F_Score'] + tabel_rfm['M_Score']
	tabel_rfm = tabel_rfm.sort_values(by=['RFM_Score'], ascending=False)

	tabel_jadi = data_driver.join(tabel_rfm.set_index('id_driver'), on='id_driver')
	tabel_jadi = tabel_jadi.fillna(1)

	#memasukkan ke dalam database
	query = "UPDATE `_driver` SET `rfm_score_driver`=%s WHERE `id_driver`=%s"
	query_rfm = "INSERT INTO `_rfm_data`(`id_simulation`, `id_batch`, `id_driver`, `recency`, `frequency`, `monetary`, `r_score`, `f_score`, `m_score`, `rfm_score`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" 

	for i in tabel_jadi.index:
	    cursor.execute(query, (tabel_jadi['RFM_Score'][i], tabel_jadi['id_driver'][i]))
	    cursor.execute(query_rfm, (this_simul.iloc[0]['id'].astype(str), this_batch.iloc[0]['id_batch'].astype(str), tabel_jadi['id_driver'][i], tabel_jadi['Recency'][i], tabel_jadi['Frequency'][i], tabel_jadi['Monetary'][i], tabel_jadi['R_Score'][i], tabel_jadi['F_Score'][i], tabel_jadi['M_Score'][i], tabel_jadi['RFM_Score'][i]))

	    dbcon.commit()

	print("RFM Driver sukses")

print(this_simul);

#Hungarian Method
if (this_simul.iloc[0]['method'] == "Hungarian Algorithm") :
	#Membuat query normalisasi dan factor data
	query_normalisasi = "INSERT INTO `_normalisasi`(id_generate_driver, id_generate_passenger, id_batch,"
	query_factor_data = "INSERT INTO `_factor_data`(id_generate_driver, id_generate_passenger, id_batch,"
	if (cekrating == True):
	    query_normalisasi +="rating,"
	    query_factor_data +="rating,"
	if (cektotal_trip == True):
	    query_normalisasi +="total_trip,"
	    query_factor_data +="total_trip,"
	if (cekcancel == True):
	    query_normalisasi +="cancellation_rate,"
	    query_factor_data +="cancellation_rate,"
	if (cekrfmd == True):
	    query_normalisasi +="rfm_score_driver,"
	    query_factor_data +="rfm_score_driver,"
	if (cekrfmp == True):
	    query_normalisasi +="rfm_score_pass,"
	    query_factor_data +="rfm_score_pass,"
	if (cekdis == True):
	    query_normalisasi +="distance,"
	    query_factor_data +="distance,"
	if (cekdur == True):
	    query_normalisasi +="duration,"
	    query_factor_data +="duration,"
	if (cektotaldis == True):
	    query_normalisasi +="total_distance,"
	    query_factor_data +="total_distance,"

	query_normalisasi = query_normalisasi[:-1]
	query_normalisasi+=") VALUES ("

	query_factor_data = query_factor_data[:-1]
	query_factor_data+=") VALUES ("
	j=0

	while j != count+3:
	    query_normalisasi+=("%s,")
	    query_factor_data+=("%s,")
	    j+=1
	query_normalisasi = query_normalisasi[:-1]
	query_normalisasi+=")"

	query_factor_data = query_factor_data[:-1]
	query_factor_data+=")"

	query_factor_data

	#normalisasi distance dan duration
	def norm_dis_dur(mat):
	    max = 0
	    min = 20000
	    for i in gen_passengers.index:
	        for j in gen_drivers.index:
	            if (mat[i][j] > max) :
	                max = mat[i][j]
	            if (mat[i][j] < min) :
	                min = mat[i][j]
	    
	    for i in gen_passengers.index:
	        for j in gen_drivers.index:
	            mat[i][j] = (mat[i][j]-min)/(max-min)

	temp = []
	matrixhasil = []
	#Menyambungkan ke google maps dan menghitung distance
	API_key = 'AIzaSyD7UeY754uJ9BcqDh8ARv7NOCwu5EtVXMQ' 
	gmaps = googlemaps.Client(key=API_key)
	destinations = []
	list_dist = {}
	list_dur = {}
	list_distance = {}
	list_duration = {}

	h_dist_list = []
	h_dur_list = []
	temp2 = []
	# case_list = {}
	for i in gen_passengers.index:
	#     case_list[gen_passengers['id'][i]] = {}
	    list_distance[gen_passengers['id'][i]] = {}
	    list_duration[gen_passengers['id'][i]] = {}
	    list_dist[gen_passengers['id'][i]] = {}
	    list_dur[gen_passengers['id'][i]] = {}
	    temp = []
	    temp2 = []
	    for j in gen_drivers.index:
	        matrix = gmaps.distance_matrix([str(gen_drivers['lat'][j]) + ' ' + str(gen_drivers['lng'][j])], [str(gen_passengers['lat_origin'][i]) + ' ' + str(gen_passengers['lng_origin'][i])], mode="driving")['rows'][0]['elements'][0]
	#         print(matrix)
	        list_distance[gen_passengers['id'][i]][gen_drivers['id'][j]] = matrix['distance']['text']
	        list_duration[gen_passengers['id'][i]][gen_drivers['id'][j]] = matrix['duration']['text']
	        list_dist[gen_passengers['id'][i]][gen_drivers['id'][j]] = matrix['distance']['value']
	        list_dur[gen_passengers['id'][i]][gen_drivers['id'][j]] = matrix['duration']['value']
	#         case_list[gen_passengers['id'][i]][gen_drivers['id'][j]] = int(matrix['duration']['value'])  
	        temp.append(matrix['distance']['value'])   
	        temp2.append(matrix['duration']['value'])        

	    h_dist_list.append(temp)
	    h_dur_list.append(temp2)

	if(cekdis == True):
	    norm_dis_dur(h_dist_list)
	    matrixhasil = h_dist_list.copy()
	    precent = factor_used.loc[factor_used['id_factor'] == 6, 'precentage'].iloc[0]/100
	    for i in gen_passengers.index:
	        for j in gen_drivers.index:
	            matrixhasil[i][j] = matrixhasil[i][j]*precent
	    factor_used = factor_used[factor_used.id_factor != 6]

	    if(cekdur == True):
	        norm_dis_dur(h_dur_list)

	elif(cekdur == True):
	    norm_dis_dur(h_dur_list)
	    matrixhasil = h_dur_list.copy()
	    precent = factor_used.loc[factor_used['id_factor'] == 7, 'precentage'].iloc[0]/100
	    for i in gen_passengers.index:
	        for j in gen_drivers.index:
	            matrixhasil[i][j] = matrixhasil[i][j]*precent
	    factor_used = factor_used[factor_used.id_factor != 7]
	        
	        
	else :
	    for i in gen_passengers.index:
	        temp=[]
	        for j in gen_drivers.index:
	            col_name = factor_used.iloc[0]['name']
	            if(factor_used.iloc[0]['user_type']=="driver"):
	                temp.append(gen_drivers[col_name][j]*factor_used.iloc[0]['precentage']/100)
	            elif(factor_used.iloc[0]['user_type']=="passenger"):
	                temp.append(gen_passengers[col_name][i]*factor_used.iloc[0]['precentage']/100)
	        matrixhasil.append(temp)
	    factor_used = factor_used.iloc[1: , :]
	    
	h_dist_list

	#Menambahkan seluruh faktor normalisasi dan menyimpan tabel factor_data+normalisasi
	if(factor_used.empty == False):
	    for i in gen_passengers.index:
	        for j in gen_drivers.index:
	            list_normalisasi = []
	            list_faktor_data = []
	            
	            list_faktor_data.append(gen_drivers["id"][j])
	            list_faktor_data.append(gen_passengers["id"][i])
	            list_faktor_data.append(gen_drivers["id_batch"][j])
	            
	            list_normalisasi.append(gen_drivers["id"][j])
	            list_normalisasi.append(gen_passengers["id"][i])
	            list_normalisasi.append(gen_drivers["id_batch"][j])
	            
	            if (cekrating == True):
	                list_normalisasi.append(gen_drivers["rating"][j])
	                list_faktor_data.append(ori_gen_drivers["rating"][j])
	            if (cektotal_trip == True):
	                list_normalisasi.append(gen_drivers["total_trip"][j])
	                list_faktor_data.append(ori_gen_drivers["total_trip"][j])
	            if (cekcancel == True):
	                list_normalisasi.append(gen_drivers["cancellation_rate"][j])
	                list_faktor_data.append(ori_gen_drivers["cancellation_rate"][j])
	            if (cekrfmd == True):
	                list_normalisasi.append(gen_drivers["rfm_score_driver"][j])
	                list_faktor_data.append(ori_gen_drivers["rfm_score_driver"][j])
	            if (cekrfmp == True):
	                list_normalisasi.append(gen_passengers["rfm_score_pass"][i])
	                list_faktor_data.append(ori_gen_passengers["rfm_score_pass"][i])
	            if (cekdis == True):
	                list_faktor_data.append(list_dist[gen_passengers["id"][i]][gen_drivers["id"][j]])
	                list_normalisasi.append(h_dist_list[i][j])
	            if (cekdur == True):
	                list_faktor_data.append(list_dur[gen_passengers["id"][i]][gen_drivers["id"][j]])
	                list_normalisasi.append(h_dur_list[i][j])
	            if (cektotaldis == True):
	                list_normalisasi.append(gen_drivers["total_distance"][j])
	                list_faktor_data.append(ori_gen_drivers["total_distance"][j])
	            
	            cursor.execute(query_factor_data, list_faktor_data)
	            cursor.execute(query_normalisasi, list_normalisasi)
	            
	            dbcon.commit()
	            
	            for k in factor_used.index:
	                col_name = factor_used['name'][k]
	                if(factor_used['user_type'][k]=="driver"):
	                    matrixhasil[i][j] += factor_used['precentage'][k]/100*gen_drivers[col_name][j]
	#                     print(factor_used['precentage'][k]/100)
	#                     print(gen_drivers[col_name][j])
	                if(factor_used['user_type'][k]=="passenger"):
	                    matrixhasil[i][j] += factor_used['precentage'][k]/100*gen_passengers[col_name][i]
	                if(col_name=="duration" and cekdis == True):
	                    matrixhasil[i][j] += factor_used['precentage'][k]/100*h_dur_list[i][j]
	                
	matrixhasil        

	#Mengecek apakah matrix square? Jika tidak, ditambahkan 0 untuk melengkapi
	if(gen_passengers.index.stop < gen_drivers.index.stop):
	    r = gen_drivers.index.stop-gen_passengers.index.stop
	    r += gen_passengers.index.stop
	    for i in range(gen_passengers.index.stop, r):
	        temp = []
	        for j in range(gen_drivers.index.stop):
	            temp.append(0)
	        matrixhasil.append(temp)
	        
	if(gen_passengers.index.stop > gen_drivers.index.stop):
	    r = gen_passengers.index.stop-gen_drivers.index.stop
	    r += gen_drivers.index.stop
	    for i in range(gen_passengers.index.stop):
	        for j in range(gen_drivers.index.stop, r):
	            matrixhasil[i].append(0)

	#Perhitungan Hungarian
	row_ind, col_ind = linear_sum_assignment(matrixhasil)
	hasil_match = pd.DataFrame(columns=["Passenger", "Driver", "Cost"])
	for i in range(len(row_ind)):
	    hasil_match.loc[i] = [int(row_ind[i]), int(col_ind[i]), matrixhasil[row_ind[i]][col_ind[i]]]

	#Membuat tabel Hasil Match
	hasil_match = pd.merge(hasil_match,gen_passengers[['id']], left_on='Passenger', right_index=True)
	hasil_match = hasil_match.rename(columns={'id': 'Id Generate Passenger'})
	hasil_match = pd.merge(hasil_match,gen_drivers[['id']], left_on='Driver', right_index=True)
	hasil_match = hasil_match.rename(columns={'id': 'Id Generate Driver'})
	hasil_match = hasil_match.drop(columns="Passenger")
	hasil_match = hasil_match.drop(columns="Driver")
	hasil_match

	#Menghitung hasil pick up time
	hasil_match['pick_time']=""
	hasil_match['Status']=""

	for i in hasil_match.index:
	    #value 491 = 8 menit
	    if(cekdur==True | cekdis == True):
	        hasil_match['Status'][i] = np.where(list_dur[hasil_match["Id Generate Passenger"][i]][hasil_match["Id Generate Driver"][i]] <= 491, "1", "0")
	    else:
	        hasil_match['Status'] = "1"
	    dur, temp = str(list_duration[hasil_match["Id Generate Passenger"][i]][hasil_match["Id Generate Driver"][i]]).split(" ")
	    pick_time = datetime.now() + relativedelta(minutes=int(dur))
	    hasil_match['pick_time'][i]=pick_time.strftime("%Y-%m-%d %H:%M:%S")
	hasil_match

	#Menghitung Duration, distance dan price
	gen_passengers['duration']=""
	gen_passengers['distance']=""
	gen_passengers['price']=""

	for i in gen_passengers.index:
	    matrix = gmaps.distance_matrix([str(gen_passengers['lat_destination'][i]) + ' ' + str(gen_passengers['lng_destination'][i])], [str(gen_passengers['lat_origin'][i]) + ' ' + str(gen_passengers['lng_origin'][i])])
	    gen_passengers['distance'][i]=str(matrix['rows'][0]['elements'][0]['distance']['text'])
	    gen_passengers['duration'][i]=str(matrix['rows'][0]['elements'][0]['duration']['text'])

	for i in gen_passengers.index:
	    distance, temp = str(gen_passengers['distance'][i]).split(" ")
	    price = float(distance)*2500
	    gen_passengers['price'][i]=price
	gen_passengers

	#Menghitung Arrived time
	hasil_match = pd.merge(hasil_match, gen_passengers, left_on='Id Generate Passenger', right_on='id')
	hasil_match['arrived_time']=""

	for i in hasil_match.index:
	    dur, temp = str(hasil_match['duration'][i]).split(" ")
	    t1 = datetime.strptime(hasil_match['pick_time'][i], '%Y-%m-%d %H:%M:%S')
	    arr_time = t1 + relativedelta(minutes=int(dur))
	    hasil_match['arrived_time'][i] = arr_time.strftime("%Y-%m-%d %H:%M:%S")
	hasil_match

	#Memasukkan data ke dalam database
	sql = "INSERT INTO `_assignment`(`id_simulation`, `id_batch`, `id_generate_driver`, `id_generate_passenger`, `assigned_timestamp`, `pickup_duration`, `pickup_distance`, `pickup_timestamp`, `trip_duration`, `trip_distance`, `arrived_timestamp`, `price`, `status`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"
	query_driver = "UPDATE `_generate_drivers` SET `status`=0 WHERE `id`=%s"
	query_pass = "UPDATE `_generate_passengers` SET `status`=0 WHERE `id`=%s"

	for i in hasil_match.index:
	    status = hasil_match['Status'][i]
	    value = "VALUES"
	    
	    if (status!="0"):
	        simulation_id = hasil_match['id_simulation'][i]
	        batch_id = hasil_match['id_batch'][i]
	        gen_driver = hasil_match['Id Generate Driver'][i]
	        gen_cust = hasil_match['Id Generate Passenger'][i]
	        assigned_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
	        pickup_dur = list_duration[hasil_match["id"][i]][hasil_match["Id Generate Driver"][i]]
	        pickup_dist = list_distance[hasil_match["id"][i]][hasil_match["Id Generate Driver"][i]]
	        pick_time = hasil_match['pick_time'][i]
	        trip_dur = hasil_match['duration'][i]
	        trip_distance = hasil_match['distance'][i]
	        arrived_time = hasil_match['arrived_time'][i]
	        price = hasil_match['price'][i]
	            
	        cursor.execute(sql, (simulation_id, batch_id, gen_driver, gen_cust, assigned_time, pickup_dur, pickup_dist, pick_time, trip_dur, trip_distance, arrived_time, price, status))
	        cursor.execute(query_driver, (gen_driver))
	        cursor.execute(query_pass, (gen_cust))
	        dbcon.commit()


	#Menghitung runtime batch
	end = time.time()
	total = end-begin

	print("Total runtime of the program is " + str(total))

	query_runtime = "UPDATE `_batch` SET `run_time`=%s WHERE `id_batch`=%s"
	cursor.execute(query_runtime, (total, this_batch.iloc[0]['id_batch'].astype(str)))
	dbcon.commit()
	dbcon.close()