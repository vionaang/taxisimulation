from __future__ import print_function
import os
import numpy as np
import pandas as pd
import googlemaps
from datetime import datetime
import time
import pymysql
from dateutil.relativedelta import relativedelta
from sklearn import preprocessing
from scipy.optimize import linear_sum_assignment
import mysql.connector
import random
import copy

from datetime import timedelta

begin = time.time()

db = mysql.connector.connect(
    host="localhost",
    user="root",
    passwd="admin",
    database="skripsi"
    )
if db.is_connected():
    print("Berhasil terhubung ke database")

    cursor = db.cursor(buffered=True)

# GET ID SIMULATION COMPARISON
cursor.execute("SELECT id FROM _simulation WHERE status = 1 LIMIT 1")
id_simulation = cursor.fetchone()[0] 

# GET TIMESTAMP
ts = time.time()    
timestamp = datetime.fromtimestamp(ts).strftime('%Y-%m-%d %H:%M:%S')

#Connect Database
hostname = 'localhost'
username = 'root'
password = 'admin'
database = 'skripsi'
dbcon  = pymysql.connect(host=hostname,user=username,password=password,db=database)
cursor = dbcon.cursor()

#Get simulation
try:
    SQL_Query = pd.read_sql_query(
        'select * from _simulation where status=1', dbcon)

    this_simul = pd.DataFrame(SQL_Query, columns=['id', 'simulation_name', 'method', 'start_date','end_date','start_hour','end_hour','status','id_main_simulation'])
except:
    print("Error: unable to convert the data")

#GET ID BATCH
query_batch = 'SELECT * FROM `_batch` WHERE is_current_batch = 1 AND `id_simulation`=%s'
cursor.execute(query_batch,this_simul.iloc[0]['id'].astype(str))
batch = cursor.fetchone()

query_batch = 'SELECT * FROM `_batch` WHERE `id_simulation`=%s AND batch_num = %s'
cursor.execute(query_batch,(this_simul.iloc[0]['id_main_simulation'],batch[2]))
batch_main = cursor.fetchone()

query_simul = 'SELECT * FROM `_simulation` WHERE `id`=%s'
cursor.execute(query_simul,(this_simul.iloc[0]['id_main_simulation']))
simul_main = cursor.fetchone()

# query = "UPDATE `_generate_drivers` SET `status`=1 WHERE `id_simulation`=%s AND id_batch = %s" 
# cursor.execute(query, (id_simulation_main, id_batch_main))
# dbcon.commit()

# query = "UPDATE `_generate_passengers` SET `status`=1 WHERE `id_simulation`=%s AND id_batch = %s"
# cursor.execute(query, (id_simulation_main, id_batch_main))
# dbcon.commit()

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

ori_factor_used = factor_used.copy()

pd.set_option('display.max_columns', None)


#Fungsi normalisasi
def normalisasi(df,col_name, goal):
    min = df[col_name].min()
    max = df[col_name].max()
    df[col_name] = df[col_name].astype(float)
    
    for i in df.index:
        normalisasi = float((df[col_name][i]-min)/(max-min))
        if(pd.isna(normalisasi)):
            normalisasi=0
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
        if(factor_used['name'][i]=="rfm_score_pass"):
            cekrfmp = True
            count+=1
    elif(factor_used['name'][i]=="distance"):
        cekdis = True
        count+=1
    elif(factor_used['name'][i]=="duration"):
        cekdur = True
        count+=1

start_rfm = time.time()

if(cekrfmp==True):

    #mengambil data simulasi
    this_simul = pd.read_sql_query(
    'select id from _simulation where status=1', dbcon)

    this_batch = pd.read_sql_query(
    'SELECT batch_num, id_batch FROM _batch WHERE id_simulation ='+ this_simul.iloc[0]['id'].astype(str) +' ORDER BY batch_num DESC Limit 1', dbcon)

    #Mengambil data factor_used
    factor = pd.read_sql_query(
    'select * from _factor_used where `id_simulation`=' + this_simul.iloc[0]['id'].astype(str) + '&& id_factor=8', dbcon)

    data_cust = pd.read_sql_query(
    'select * from _generate_passengers where id_simulation=' + this_simul.iloc[0]['id'].astype(str) + "&& id_batch=" + this_batch.iloc[0]['id_batch'].astype(str), dbcon)

    string = ", ".join(data_cust.id_passenger.astype(str).tolist())
    print("Id passenger RFM : " + string)

    data_transaksi = pd.read_sql_query(
        'select * from _transaksi', dbcon)

    #Hitung recency
    data_recency = data_transaksi.groupby(by='id_passenger', as_index=False)['transaction_date'].max()
    data_recency.columns = ['id_passenger','LastPurchaseDate']

    now=simul_main[3]
    my_time = datetime.min.time()
    now = datetime.combine(now, my_time)
    data_recency['Recency']=""
    for i in data_recency.index:
        data_recency['Recency'][i] = (now-data_recency['LastPurchaseDate'][i]).days

    #jika RFM dimodifikasi
    if(factor.iloc[0]['rfm1'] != 0 or factor.iloc[0]['rfm2'] != 0 or factor.iloc[0]['rfm3'] != 0 ) :
        #Menghitung Kategori FM1
        day = int(factor.iloc[0]['rfm1'])
        tanggal = now - timedelta(days=day)
        print("FM Kategori 1 : " +tanggal.strftime('%Y-%m-%d'))

        data_transaksi1 = data_transaksi.copy()
        data_transaksi1 = data_transaksi1.query("transaction_date >= @tanggal.strftime('%Y-%m-%d')")
        data_transaksi1.sort_values(by=['transaction_date'])

        data_frequency1 = data_transaksi1.groupby(by=['id_passenger'], as_index=False)['transaction_date'].count()
        data_frequency1.columns = ['id_passenger','Frequency']
        data_monetary1 = data_transaksi1.groupby(by=['id_passenger'], as_index=False)['price'].sum()
        data_monetary1.columns = ['id_passenger','Monetary']

        #Menghitung Kategori FM2
        day = int(factor.iloc[0]['rfm2'])
        tanggal = now - timedelta(days=day)
        print("FM Kategori 2 : " +tanggal.strftime('%Y-%m-%d'))

        data_transaksi2 = data_transaksi.copy()
        data_transaksi2 = data_transaksi2.query("transaction_date >= @tanggal.strftime('%Y-%m-%d')")
        data_transaksi2.sort_values(by=['transaction_date'])

        data_frequency2 = data_transaksi2.groupby(by=['id_passenger'], as_index=False)['transaction_date'].count()
        data_frequency2.columns = ['id_passenger','Frequency']
        data_monetary2 = data_transaksi2.groupby(by=['id_passenger'], as_index=False)['price'].sum()
        data_monetary2.columns = ['id_passenger','Monetary']


        data_frequency = pd.merge(data_frequency1, data_frequency2, on="id_passenger")
        data_frequency.columns = ['id_passenger','Frequency1', 'Frequency2']

        data_monetary = pd.merge(data_monetary1, data_monetary2, on="id_passenger")
        data_monetary.columns = ['id_passenger','Monetary1', 'Monetary2']

        #Menghitung Kategori FM3
        day = int(factor.iloc[0]['rfm3'])
        tanggal = now - timedelta(days=day)
        print("FM Kategori 3 : " + tanggal.strftime('%Y-%m-%d'))

        data_transaksi3 = data_transaksi.copy()
        data_transaksi3 = data_transaksi3.query("transaction_date >= @tanggal.strftime('%Y-%m-%d')")
        data_transaksi3.sort_values(by=['transaction_date'])

        data_frequency3 = data_transaksi3.groupby(by=['id_passenger'], as_index=False)['transaction_date'].count()
        data_frequency3.columns = ['id_passenger','Frequency']
        data_monetary3 = data_transaksi3.groupby(by=['id_passenger'], as_index=False)['price'].sum()
        data_monetary3.columns = ['id_passenger','Monetary']

        data_frequency = pd.merge(data_frequency, data_frequency3, on="id_passenger")
        data_frequency.columns = ['id_passenger','Frequency1', 'Frequency2', 'Frequency3']
        data_monetary = pd.merge(data_monetary, data_monetary3, on="id_passenger")
        data_monetary.columns = ['id_passenger','Monetary1', 'Monetary2', 'Monetary3']

        data_frequency["Frequency"] = ""
        data_monetary["Monetary"]=""

        #Menghitung bobot masing-masing dengan mengkalikan 10,30,60 persen
        for i in data_frequency.index: 
            hasil_freq = ((data_frequency['Frequency1'][i]*10/100)+(data_frequency['Frequency2'][i]*30/100)+(data_frequency['Frequency3'][i]*60/100))
            data_frequency['Frequency'][i] = hasil_freq
            hasil_monet = ((data_monetary['Monetary1'][i]*10/100)+(data_monetary['Monetary2'][i]*30/100)+(data_monetary['Monetary3'][i]*60/100))
            data_monetary['Monetary'][i] = hasil_monet

    else:
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
    pd.set_option('max_columns', None)

    #Membuat pembagian quantiles frekuensi dan monetary
    quantiles = tabel_rfm.quantile(q=[0.2,0.4,0.6, 0.8],numeric_only=False)
    quantiles.to_dict() 
    # print(quantiles)

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

    tabel_rfm['RFM_Score'] = tabel_rfm['R_Score'] + tabel_rfm['F_Score'] + tabel_rfm['M_Score']
    # print(tabel_rfm.loc[tabel_rfm['id_passenger'] == 34])
    tabel_rfm = tabel_rfm.sort_values(by=['RFM_Score'], ascending=False)       
    tabel_jadi = pd.merge(data_cust, tabel_rfm, how='inner', on=['id_passenger'])
    tabel_jadi = tabel_jadi.fillna(1)
    
    print(tabel_jadi)
    #memasukkan ke dalam database
    query = "UPDATE `_passenger` SET `rfm_score_pass`=%s WHERE `id_passenger`=%s"
    query_rfm = "INSERT INTO `_rfm_data`(`id_simulation`, `id_batch`, `id_pass`, `recency`, `frequency`, `monetary`, `r_score`, `f_score`, `m_score`, `rfm_score`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" 
    query_freq = "INSERT INTO `_rfm_freq`(`id_simulation`, `id_batch`, `id_passenger`, `freq1`, `freq2`, `freq3`, `freq_total`) VALUES (%s,%s,%s,%s,%s,%s,%s)" 
    query_monet = "INSERT INTO `_rfm_monet`(`id_simulation`, `id_batch`, `id_passenger`, `monet1`, `monet2`, `monet3`, `monet_total`) VALUES (%s,%s,%s,%s,%s,%s,%s)" 
    query_quantiles = "INSERT INTO `_rfm_quantile`(`id_simulation`, `id_batch`, quantile, `frequency`, `monetary`, type) VALUES (%s, %s, %s, %s,%s,%s)"

    id_simul = this_simul.iloc[0]['id'].astype(int)
    id_batch = this_batch.iloc[0]['id_batch'].astype(int)

    # print(quantiles)


    # for i in quantiles.index:
        # cursor.execute(query_quantiles, (id_simul, id_batch, i, quantiles['Frequency'][i], quantiles['Monetary'][i], "Passenger"))
        # dbcon.commit()

    # for i in tabel_jadi.index:
        # cursor.execute(query, (tabel_jadi['RFM_Score'][i], tabel_jadi['id_passenger'][i]))
        # cursor.execute(query_rfm, (id_simul, id_batch, tabel_jadi['id_passenger'][i], tabel_jadi['Recency'][i], tabel_jadi['Frequency'][i], tabel_jadi['Monetary'][i], tabel_jadi['R_Score'][i], tabel_jadi['F_Score'][i], tabel_jadi['M_Score'][i], tabel_jadi['RFM_Score'][i]))
        # if(factor.iloc[0]['rfm1'] != 0 or factor.iloc[0]['rfm2'] != 0 or factor.iloc[0]['rfm3'] != 0 ):
        #     cursor.execute(query_freq, (id_simul, id_batch, tabel_jadi['id_passenger'][i], tabel_jadi['Frequency1'][i],tabel_jadi['Frequency2'][i],tabel_jadi['Frequency3'][i],tabel_jadi['Frequency'][i]))
        #     cursor.execute(query_monet, (id_simul, id_batch, tabel_jadi['id_passenger'][i], tabel_jadi['Monetary1'][i],tabel_jadi['Monetary2'][i],tabel_jadi['Monetary3'][i],tabel_jadi['Monetary'][i]))
        # dbcon.commit()

    print("RFM Passenger sukses")

if(cekrfmd == True):
    this_simul = pd.read_sql_query(
            'select id from _simulation where status=1', dbcon)

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

    now=simul_main[3]
    my_time = datetime.min.time()
    now = datetime.combine(now, my_time)
    data_recency['Recency']=""
    for i in data_recency.index:
        data_recency['Recency'][i] = (now-data_recency['LastJobDate'][i]).days

    data_recency

    #jika RFM dimodifikasi
    now = datetime.now()
    if(factor.iloc[0]['rfm1'] != 0 or factor.iloc[0]['rfm2'] != 0 or factor.iloc[0]['rfm3'] != 0 ) :
        #Menghitung Kategori FM1
        day = int(factor.iloc[0]['rfm1'])
        tanggal = now - timedelta(days=day)
        print("FM Kategori 1 : " +tanggal.strftime('%Y-%m-%d'))

        data_transaksi1 = data_transaksi.copy()
        data_transaksi1 = data_transaksi1.query("transaction_date >= @tanggal.strftime('%Y-%m-%d')")
        data_transaksi1.sort_values(by=['transaction_date'])

        data_frequency1 = data_transaksi1.groupby(by=['id_driver'], as_index=False)['transaction_date'].count()
        data_frequency1.columns = ['id_driver','Frequency']
        data_monetary1 = data_transaksi1.groupby(by=['id_driver'], as_index=False)['price'].sum()
        data_monetary1.columns = ['id_driver','Monetary']

        #Menghitung Kategori FM2
        day = int(factor.iloc[0]['rfm2'])
        tanggal = now - timedelta(days=day)
        print("FM Kategori 2 : " +tanggal.strftime('%Y-%m-%d'))

        data_transaksi2 = data_transaksi.copy()
        data_transaksi2 = data_transaksi2.query("transaction_date >= @tanggal.strftime('%Y-%m-%d')")
        data_transaksi2.sort_values(by=['transaction_date'])

        data_frequency2 = data_transaksi2.groupby(by=['id_driver'], as_index=False)['transaction_date'].count()
        data_frequency2.columns = ['id_driver','Frequency']
        data_monetary2 = data_transaksi2.groupby(by=['id_driver'], as_index=False)['price'].sum()
        data_monetary2.columns = ['id_driver','Monetary']


        data_frequency = pd.merge(data_frequency1, data_frequency2, on="id_driver")
        data_frequency.columns = ['id_driver','Frequency1', 'Frequency2']

        data_monetary = pd.merge(data_monetary1, data_monetary2, on="id_driver")
        data_monetary.columns = ['id_driver','Monetary1', 'Monetary2']

        #Menghitung Kategori FM3
        day = int(factor.iloc[0]['rfm3'])
        tanggal = now - timedelta(days=day)
        print("FM Kategori 3 : " + tanggal.strftime('%Y-%m-%d'))

        data_transaksi3 = data_transaksi.copy()
        data_transaksi3 = data_transaksi3.query("transaction_date >= @tanggal.strftime('%Y-%m-%d')")
        data_transaksi3.sort_values(by=['transaction_date'])

        data_frequency3 = data_transaksi3.groupby(by=['id_driver'], as_index=False)['transaction_date'].count()
        data_frequency3.columns = ['id_driver','Frequency']
        data_monetary3 = data_transaksi3.groupby(by=['id_driver'], as_index=False)['price'].sum()
        data_monetary3.columns = ['id_driver','Monetary']

        data_frequency = pd.merge(data_frequency, data_frequency3, on="id_driver")
        data_frequency.columns = ['id_driver','Frequency1', 'Frequency2', 'Frequency3']
        data_monetary = pd.merge(data_monetary, data_monetary3, on="id_driver")
        data_monetary.columns = ['id_driver','Monetary1', 'Monetary2', 'Monetary3']

        data_frequency["Frequency"] = ""
        data_monetary["Monetary"]=""

        #Menghitung bobot masing-masing dengan mengkalikan 10,30,60 persen
        for i in data_frequency.index: 
            hasil_freq = ((data_frequency['Frequency1'][i]*10/100)+(data_frequency['Frequency2'][i]*30/100)+(data_frequency['Frequency3'][i]*60/100))
            data_frequency['Frequency'][i] = hasil_freq
            hasil_monet = ((data_monetary['Monetary1'][i]*10/100)+(data_monetary['Monetary2'][i]*30/100)+(data_monetary['Monetary3'][i]*60/100))
            data_monetary['Monetary'][i] = hasil_monet

    else:
        #Hitung frekuensi
        data_frequency = data_transaksi.groupby(by=['id_driver'], as_index=False)['transaction_date'].count()
        data_frequency.columns = ['id_driver','Frequency']

        #hitung monetary
        data_monetary = data_transaksi.groupby(by='id_driver',as_index=False)['price'].sum()
        data_monetary.columns = ['id_driver','Monetary']

    #membuat pembagian recency
    tabel_rfm = data_recency.merge(data_frequency,on='id_driver')
    tabel_rfm = tabel_rfm.merge(data_monetary,on='id_driver')

    tabel_rfm['R_Score'] = np.where(tabel_rfm['Recency'] <= factor.iloc[0]['recency1'], 5, 
                                    np.where(tabel_rfm['Recency'] <= factor.iloc[0]['recency2'], 4, 
                                             np.where(tabel_rfm['Recency'] <= factor.iloc[0]['recency3'], 3,
                                                     np.where(tabel_rfm['Recency'] <= factor.iloc[0]['recency4'], 2,
                                                              np.where(tabel_rfm['Recency'] <= factor.iloc[0]['recency5'], 1, 0)))))
    pd.set_option('max_columns', None)

    #Membuat pembagian freq dan monetary
    quantiles = tabel_rfm.quantile(q=[0.2,0.4,0.6, 0.8], numeric_only=False)
    quantiles.to_dict()
    # print(quantiles)

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
    # print(tabel_jadi)

    #memasukkan ke dalam database
    query = "UPDATE `_driver` SET `rfm_score_driver`=%s WHERE `id_driver`=%s"
    query_rfm = "INSERT INTO `_rfm_data`(`id_simulation`, `id_batch`, `id_driver`, `recency`, `frequency`, `monetary`, `r_score`, `f_score`, `m_score`, `rfm_score`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" 
    query_freq = "INSERT INTO `_rfm_freq`(`id_simulation`, `id_batch`, `id_driver`, `freq1`, `freq2`, `freq3`, `freq_total`) VALUES (%s,%s, %s,%s,%s,%s,%s)" 
    query_monet = "INSERT INTO `_rfm_monet`(`id_simulation`, `id_batch`, `id_driver`, `monet1`, `monet2`, `monet3`, `monet_total`) VALUES (%s, %s, %s,%s,%s,%s,%s)" 

    id_simul = this_simul.iloc[0]['id'].astype(int)
    id_batch = this_batch.iloc[0]['id_batch'].astype(int)

    query_quantiles = "INSERT INTO `_rfm_quantile`(`id_simulation`, `id_batch`, quantile, `frequency`, `monetary`, type) VALUES (%s, %s, %s, %s,%s,%s)"

    # for i in quantiles.index:
    #     # cursor.execute(query_quantiles, (id_simul, id_batch, i, quantiles['Frequency'][i], quantiles['Monetary'][i], "Driver"))
    #     dbcon.commit()

    # for i in tabel_jadi.index:
    #     # cursor.execute(query, (tabel_jadi['RFM_Score'][i], tabel_jadi['id_driver'][i]))
    #     # cursor.execute(query_rfm, (id_simul, id_batch, tabel_jadi['id_driver'][i], tabel_jadi['Recency'][i], tabel_jadi['Frequency'][i], tabel_jadi['Monetary'][i], tabel_jadi['R_Score'][i], tabel_jadi['F_Score'][i], tabel_jadi['M_Score'][i], tabel_jadi['RFM_Score'][i]))
    #     if(factor.iloc[0]['rfm1'] != 0 or factor.iloc[0]['rfm2'] != 0 or factor.iloc[0]['rfm3'] != 0 ):
            # cursor.execute(query_freq, (id_simul, id_batch, tabel_jadi['id_driver'][i], tabel_jadi['Frequency1'][i],tabel_jadi['Frequency2'][i],tabel_jadi['Frequency3'][i],tabel_jadi['Frequency'][i]))
            # cursor.execute(query_monet, (id_simul, id_batch, tabel_jadi['id_driver'][i], tabel_jadi['Monetary1'][i],tabel_jadi['Monetary2'][i],tabel_jadi['Monetary3'][i],tabel_jadi['Monetary'][i]))
            
        # dbcon.commit()

    print("RFM Driver sukses")

end_rfm = time.time()

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

for i in factor_used.index:
    if(factor_used['user_type'][i]=="driver"):
        normalisasi(gen_drivers, factor_used['name'][i],factor_used['goal'][i])
    elif(factor_used['user_type'][i]=="passenger"):
        normalisasi(gen_passengers, factor_used['name'][i],factor_used['goal'][i])


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
API_key = 'AIzaSyDMhKBmdX4OHcPGy0n-Ver-6wOJfxIt7gE' 
gmaps = googlemaps.Client(key=API_key)
destinations = []
list_dist = {}
list_dur = {}
list_ori_dist = {}
list_ori_dur = {}

h_dist_list = []
h_dur_list = []
temp2 = []
# case_list = {}

# print(gen_passengers)
# print(gen_drivers)
# print(batch_main)
for i in gen_passengers.index:
#     case_list[gen_passengers['id'][i]] = {}
    # list_ori_dist[gen_passengers['id'][i]] = {}
    # list_ori_dur[gen_passengers['id'][i]] = {}
    list_dist[gen_passengers['id'][i]] = {}
    list_dur[gen_passengers['id'][i]] = {}
    temp = []
    temp2 = []
    for j in gen_drivers.index:
        if(cekrfmp==True and cekrfmd==True):
            query_matrix = 'SELECT n.distance,n.duration, n.rfm_score_pass, n.rfm_score_driver FROM `_factor_data` n JOIN _generate_drivers gd ON gd.id = n.id_generate_driver JOIN _generate_passengers gp ON gp.id = n.id_generate_passenger WHERE n.id_batch = %s AND gd.id_driver = %s AND gp.id_passenger = %s'
            cursor.execute(query_matrix,(batch_main[0], gen_drivers['id_driver'][j],gen_passengers['id_passenger'][i]))
            res = cursor.fetchall()
            gen_passengers['rfm_score_pass'][i]=res[0][2]
            gen_drivers['rfm_score_driver'][j]=res[0][3]
            ori_gen_passengers['rfm_score_pass'][i]=res[0][2]
            ori_gen_drivers['rfm_score_driver'][j]=res[0][3]

        else:
            query_matrix = 'SELECT n.distance,n.duration FROM `_factor_data` n JOIN _generate_drivers gd ON gd.id = n.id_generate_driver JOIN _generate_passengers gp ON gp.id = n.id_generate_passenger WHERE n.id_batch = %s AND gd.id_driver = %s AND gp.id_passenger = %s'
            cursor.execute(query_matrix,(batch_main[0], gen_drivers['id_driver'][j],gen_passengers['id_passenger'][i]))
            res = cursor.fetchall()

        list_dist[gen_passengers['id'][i]][gen_drivers['id'][j]] = res[0][0]
        list_dur[gen_passengers['id'][i]][gen_drivers['id'][j]] = res[0][1]
#         
        temp.append(res[0][0])   
        temp2.append(res[0][1])        

    h_dist_list.append(temp)
    h_dur_list.append(temp2)

if(cekdis == True):
    norm_dis_dur(h_dist_list)
    matrixhasil = copy.deepcopy(h_dist_list)
    precent = factor_used.loc[factor_used['id_factor'] == 6, 'precentage'].iloc[0]/100
    for i in gen_passengers.index:
        for j in gen_drivers.index:
            matrixhasil[i][j] = matrixhasil[i][j]*precent
    factor_used = factor_used[factor_used.id_factor != 6]

    if(cekdur == True):
        norm_dis_dur(h_dur_list)

elif(cekdur == True):
    norm_dis_dur(h_dur_list)
    matrixhasil = copy.deepcopy(h_dur_list)

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
    
# print(cekdur)
# print(factor_used)
# print(gen_passengers.index)
# print(gen_drivers.index)

# cek_waiting_pass = True
# cek_waiting_driver = True
# #Menambahkan seluruh faktor normalisasi dan menyimpan tabel factor_data+normalisasi
# norm_pass = "UPDATE `_normalisasi`SET waiting_time_p=%s where id_generate_driver=%s and id_generate_passenger=%s"
# norm_d = "UPDATE `_normalisasi`SET waiting_time_d =%s where id_generate_driver=%s and id_generate_passenger=%s"

#Menambahkan seluruh faktor normalisasi dan menyimpan tabel factor_data+normalisasi
if(ori_factor_used.empty == False):
    for i in gen_passengers.index:
        for j in gen_drivers.index:
            list_normalisasi = []
            list_faktor_data = []
            
            list_faktor_data.append(gen_drivers["id"][j])
            list_faktor_data.append(gen_passengers["id"][i])
            list_faktor_data.append(batch[0])
            
            list_normalisasi.append(gen_drivers["id"][j])
            list_normalisasi.append(gen_passengers["id"][i])
            list_normalisasi.append(batch[0])
            
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

            # selisih = id_batch-ori_gen_passengers["id_batch"][i].astype(int)
            # #waiting time
            # if(selisih>=2 and cek_waiting_pass):
            #    normalisasi(gen_passengers, 'id_batch', "min")
            #    cek_waiting_pass = False

            # if(cek_waiting_pass == False):
            #     matrixhasil[i][j] += gen_passengers["id_batch"][i]
            #     cursor.execute(norm_pass, (gen_passengers["id_batch"][i], gen_drivers["id"][j], gen_passengers["id"][i]))
            #     dbcon.commit()

            # selisih_d = id_batch-ori_gen_drivers["id_batch"][j].astype(int)

            # if(selisih_d>=2 and cek_waiting_driver):
            #     normalisasi(gen_drivers, 'id_batch', "min")
            #     cek_waiting_driver = False

            # if(cek_waiting_driver == False):
            #     matrixhasil[i][j] += gen_drivers["id_batch"][j]
            #     cursor.execute(norm_d, (gen_drivers["id_batch"][j], gen_drivers["id"][j], gen_passengers["id"][i]))
            #     dbcon.commit()
            
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
                
#Mengecek apakah matrix square? Jika tidak, ditambahkan 0 untuk melengkapi
if(len(gen_passengers) < len(gen_drivers)):
    r = len(gen_drivers)-len(gen_passengers)
    r += len(gen_passengers)
    for i in range(len(gen_passengers), r):
        temp = []
        for j in range(len(gen_drivers)):
            temp.append(0)
        matrixhasil.append(temp)
        
if(len(gen_passengers) > len(gen_drivers)):
    r = len(gen_passengers)-len(gen_drivers)
    r += len(gen_drivers)
    for i in range(len(gen_passengers)):
        for j in range(len(gen_drivers), r):
            matrixhasil[i].append(0)


#Perhitungan Hungarian
row_ind, col_ind = linear_sum_assignment(matrixhasil)
hasil_match = pd.DataFrame(columns=["Passenger", "Driver", "Cost"])
for i in range(len(row_ind)):
    hasil_match.loc[i] = [int(row_ind[i]), int(col_ind[i]), matrixhasil[row_ind[i]][col_ind[i]]]

print(batch_main[0])

#Membuat tabel Hasil Match
SQL_Query = pd.read_sql_query('SELECT a.`id_simulation`, a.`id_batch`, a.`id_generate_driver`, a.`id_generate_passenger`, a.`assigned_timestamp`, `pickup_duration`, `pickup_distance`, `pickup_timestamp`, `trip_duration`, `trip_distance`, `arrived_timestamp`, `price`, a.`status`, p.id_passenger, d.id_driver FROM `_assignment` a join _generate_passengers p on a.id_generate_passenger=p.id join _generate_drivers d on d.id=a.id_generate_driver WHERE a.id_batch='+ str(batch_main[0]), dbcon)
hasil_match = pd.DataFrame(SQL_Query, columns=['id_simulation', 'id_batch', 'id_generate_driver', 'id_generate_passenger', 'assigned_timestamp', 'pickup_duration', 'pickup_distance', 'pickup_timestamp', 'trip_duration', 'trip_distance', 'arrived_timestamp', 'price', 'status', 'id_driver', 'id_passenger'])

hasil_match = pd.merge(hasil_match,ori_gen_drivers[['id_driver', 'id']],on='id_driver', how='left')
hasil_match = hasil_match.rename(columns={'id': 'id_gen_driver'})

hasil_match = pd.merge(hasil_match,ori_gen_passengers[['id_passenger', 'id']],on='id_passenger', how='left')


for i in hasil_match.index:
    hasil_match.index

#Menghitung hasil pick up time
hasil_match['pick_time']=""
hasil_match['Status']=""
hasil_match['arrived_time']=""

print(hasil_match);
print(id_simulation);

for i in hasil_match.index:
    # if(cekdis==True):
    #     hasil_match['Status'][i] = np.where(list_dist[hasil_match["Id Generate Passenger"][i]][hasil_match["Id Generate Driver"][i]] <= 4300, "1", "0")
    # if(cekdur==True):
    #     hasil_match['Status'][i] = np.where(list_dur[hasil_match["Id Generate Passenger"][i]][hasil_match["Id Generate Driver"][i]] <= 660, "1", "0")
    # else:
    #     hasil_match['Status'] = "1"
    pick_time = datetime.now() + relativedelta(minutes=float(hasil_match['pickup_duration'][i]))
    hasil_match['pick_time'][i]=pick_time.strftime("%Y-%m-%d %H:%M:%S")

#Menghitung Arrived time

for i in hasil_match.index:
    t1 = datetime.strptime(hasil_match['pick_time'][i], '%Y-%m-%d %H:%M:%S')
    temp = float(hasil_match['trip_duration'][i])*60
    arr_time = t1 + relativedelta(seconds=int(temp))
    hasil_match['arrived_time'][i] = arr_time.strftime("%Y-%m-%d %H:%M:%S")

#Memasukkan data ke dalam database
sql = "INSERT INTO `_assignment`(`id_simulation`, `id_batch`, `id_generate_driver`, `id_generate_passenger`, `assigned_timestamp`, `pickup_duration`, `pickup_distance`, `pickup_timestamp`, `trip_duration`, `trip_distance`, `arrived_timestamp`, `price`, `status`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"
query_driver = "UPDATE `_generate_drivers` SET `status`=0 WHERE `id`=%s"
query_pass = "UPDATE `_generate_passengers` SET `status`=0 WHERE `id`=%s"

print(hasil_match)

for i in hasil_match.index:
    status = hasil_match['Status'][i]
    value = "VALUES"
    
    if (status!="0"):
        simulation_id = id_simulation
        batch_id = batch[0]
        gen_driver = hasil_match['id_gen_driver'][i]
        gen_cust = hasil_match['id'][i]
        assigned_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        pickup_dur = hasil_match['pickup_duration'][i]
        pickup_dist = hasil_match['pickup_distance'][i]
        pick_time = hasil_match['pick_time'][i]
        trip_dur = hasil_match['trip_duration'][i]
        trip_distance = hasil_match['trip_distance'][i]
        arrived_time = hasil_match['arrived_time'][i]
        price = hasil_match['price'][i]
            
        cursor.execute(sql, (simulation_id, batch_id, gen_driver, gen_cust, assigned_time, pickup_dur, pickup_dist, pick_time, trip_dur, trip_distance, arrived_time, price, 1))
        cursor.execute(query_driver, (gen_driver))
        cursor.execute(query_pass, (gen_cust))
        dbcon.commit()

#Menghitung runtime batch
end = time.time()
total = end-begin

# this_simul = pd.read_sql_query(
#       'select id, rfm_filter from _simulation where status=1', dbcon)
# this_batch = pd.read_sql_query(
#       'SELECT batch_num, id_batch FROM _batch WHERE id_simulation ='+ this_simul.iloc[0]['id'].astype(str) +' ORDER BY batch_num DESC Limit 1', dbcon)
ts = time.time()    
timestamp = datetime.fromtimestamp(ts).strftime('%Y-%m-%d %H:%M:%S')

if(cekrfmd == True or cekrfmp == True):
    rfm_time = end-start_rfm
    query_runtime = "UPDATE `_batch` SET end_time=%s, assign_time=%s, rfm_time=%s WHERE `id_batch`=%s"
    cursor.execute(query_runtime, (timestamp, total, rfm_time, id_batch))
else :
    query_runtime = "UPDATE `_batch` SET `assign_time`=%s, end_time = %s WHERE `id_batch`=%s"
    cursor.execute(query_runtime, (total,timestamp, batch[0]))
dbcon.commit()

# sql = "UPDATE _generate_drivers SET status = %s WHERE id_batch = %s"
# val = (0, batch[0])
# cursor.execute(sql, val)
# dbcon.commit()

# sql = "UPDATE _generate_passengers SET status = %s WHERE id_batch = %s"
# val = (0, batch[0])
# cursor.execute(sql, val)
# dbcon.commit()

dbcon.close()