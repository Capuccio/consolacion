DROP DATABASE IF EXISTS consolacion;

CREATE DATABASE consolacion;

USE consolacion;

CREATE TABLE parents (
	id_parents INT,
	name VARCHAR(50),
	last_name VARCHAR(50),
	phone VARCHAR(50),
	address VARCHAR(50),
	password TEXT,
	PRIMARY KEY(id_parents)
);

CREATE TABLE students (
	id_students INT,
	parent INT,
	student_name VARCHAR(50),
	student_last_name VARCHAR(50),
	year VARCHAR(9),
	year_and_mention VARCHAR(50),
	PRIMARY KEY(id_students),
	FOREIGN KEY(parent) REFERENCES parents(id_parents)
);

CREATE TABLE payments (
	bill INT AUTO_INCREMENT,
	parent INT,
	type VARCHAR(13),
	reference_number INTEGER(30),
	bank VARCHAR(50),
	unit_price FLOAT(8),
	total FLOAT(8),
	date DATE,
	observations TEXT,
	PRIMARY KEY(bill),
	FOREIGN KEY(parent) REFERENCES parents(id_parents)
);

CREATE TABLE months (
	id_month INT AUTO_INCREMENT,
	month VARCHAR(11),
	PRIMARY KEY(id_month)
);

CREATE TABLE student_month_payment (
	id_student_month_payment INT AUTO_INCREMENT,
	student_month_payment_id_month INT,
	student_month_payment_id_students INT,
	student_month_payment_bill INT,
	PRIMARY KEY(id_student_month_payment),
	FOREIGN KEY(student_month_payment_id_month) REFERENCES months(id_month),
	FOREIGN KEY(student_month_payment_id_students) REFERENCES students(id_students),
	FOREIGN KEY(student_month_payment_bill) REFERENCES payments(bill)
);

CREATE TABLE rate_of_day (
	id_rate_of_day INT AUTO_INCREMENT,
	rate FLOAT(8),
	date DATE,
	PRIMARY KEY(id_rate_of_day)
);

INSERT INTO months (month) VALUES ('Enero'), ('Febrero'), ('Marzo'), ('Abril'), ('Mayo'), ('Junio'), ('Julio'), ('Agosto'), ('Septiembre'), ('Octubre'), ('Noviembre'), ('Diciembre');