create table question (
    id int(11) auto_increment primary key,
    question text,
    student_question bool
);

create table answer (
    id int auto_increment primary key,
    tutor_request_id int,
    tutor_id int,
    question_id int,
    answer text
);

create table available (
    id int auto_increment primary key,
    student_id int,
    tutor_id int,
    day int,
    period int
);

create table student (
    id int auto_increment primary key,
    name varchar(100),
    address text,
    phone varchar(12),
    email varchar(255),
    grade int,
    username varchar(50), /* first.last */
    password varchar(50),
    signed_contract bool default 0,
    notes text
);

create table tutor_request (
    id int auto_increment primary key,
    student_id int,
    teacher varchar(100),
    subject int,
    times_per_week int,
    notes text
);

create table tutor (
    id int auto_increment primary key,
    name varchar(100),
    address text,
    phone varchar(12),
    email varchar(255),
    username varchar(50), /* first.last */
    password varchar(50),
    tutor_category int,
    grade int,
    total_hours_desired int,
    signed_confidentiality bool,
    enabled bool,
    notes text
);

create table tutor2subject (
    id int auto_increment primary key,
    tutor_id int,
    subject_id int
);

create table tutor_request2category (
    id int auto_increment primary key,
    request_id int,
    category_id int
);

create table subject (
    id int auto_increment primary key,
    subject_name varchar(255)
);

create table tutor_category (
    id int auto_increment primary key,
    category_name varchar(255)
);

create table match_made (
    id int auto_increment primary key,
    start_date date,
    end_date date,
    request_id int,
    tutor_id int,
    student_acknowledged datetime,
    tutor_acknowledged datetime,
    match_made date,
    student_ack_note text,
    tutor_ack_note text,
    notes text,
    active bool
);

create table match_time (
    id int auto_increment primary key,
    match_id int,
    day int,
    period int
);

create table admin (
    name varchar(50),
    username varchar(50), /* first.last */
    password varchar(50)
);
