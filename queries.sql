USE YetiCave;

INSERT INTO cats
SET name = 'Доски и лыжи', code = 'boards';
INSERT INTO cats
SET name = 'Крепления', code = 'attachment';
INSERT INTO cats
SET name = 'Ботинки', code = 'boots';
INSERT INTO cats
SET name = 'Одежда', code = 'clothing';
INSERT INTO cats
SET name = 'Инструменты', code = 'tools';
INSERT INTO cats
SET name = 'Разное', code = 'other';

INSERT INTO users
SET dt_reg = NOW(), email ='irina_vlad@mail.ru', name = 'flower11', password = 'gfds13io84t', contact = 'https://ok.ru/irina_flower11';
INSERT INTO users
SET dt_reg = NOW(), email ='pavel1278@mail.ru', name = 'pavel1278', password = 'gert54vb12qw', contact = 'https://ok.ru/pave157';

INSERT INTO lots 
SET dt_add = NOW(), name = '2014 Rossignol District Snowboard', descr = 'Многоцелевые доски Rossignol категории Фристайл очень прочные для амплитудных приземлений и имеют более широкую стойку с новейшей джиббинговой технологией и изогнутыми кантами Magne-Traction. ',
 img_url = 'img/lot-1.jpg', dt_expired = DATE_ADD(NOW(), INTERVAL 11 DAY), price = 10999, bet_step = '500', cat_id = '1', author_id = '1';
INSERT INTO lots 
SET dt_add = NOW(), name = 'DC Ply Mens 2016/2017 Snowboard', descr = 'Популярный сноуборд Ply по ощущениям немного напоминает скейтборд.',
 img_url = 'img/lot-2.jpg', dt_expired = DATE_ADD(NOW(), INTERVAL 16 DAY), price = 159999, bet_step = '1000', cat_id = '1', author_id = '1';
INSERT INTO lots 
SET dt_add = NOW(), name = 'Крепления Union Contact Pro 2015 года размер L/XL', descr = 'Эти крепления ежегодно проверяет на прочность один из самых титулованных бэккантри-райдеров - австриец Gigi Rüf.',
 img_url = 'img/lot-3.jpg', dt_expired = DATE_ADD(NOW(), INTERVAL 7 DAY), price = 8000, bet_step = '200', cat_id = '2', author_id = '1';
INSERT INTO lots 
SET dt_add = NOW(), name = 'Ботинки для сноуборда DC Mutiny Charocal', descr = 'Прогрессивный дизайн в классическом силуэте - ботинки DC Mutiny созданы для комфортного катания и высокой производительности.',
 img_url = 'img/lot-4.jpg', dt_expired = DATE_ADD(NOW(), INTERVAL 2 WEEK), price = 10999, bet_step = '500', cat_id = '3', author_id = '2';
INSERT INTO lots 
SET dt_add = NOW(), name = 'Куртка для сноуборда DC Mutiny Charocal', descr = 'Куртка подходит для сноубординга (сноуборда) и активного отдыха. Куртка утепленная.',
 img_url = 'img/lot-5.jpg', dt_expired = DATE_ADD(NOW(), INTERVAL 3 DAY), price = 7500, bet_step = '100', cat_id = '4', author_id = '2';
INSERT INTO lots 
SET dt_add = NOW(), name = 'Маска Oakley Canopy', descr = 'Сноубордическая маска. Технология вентиляции O-Flow Arch и прослойка из микрофлиса.',
 img_url = 'img/lot-6.jpg', dt_expired = DATE_ADD(NOW(), INTERVAL 1 WEEK), price = 5400, bet_step = '100', cat_id = '6', author_id = '1';

INSERT INTO bets
SET dt_add = NOW(), price = 11200, user_id = 2, lot_id = 1;
INSERT INTO bets
SET dt_add = NOW(), price = 10200, user_id = 1, lot_id = 4;
INSERT INTO bets
SET dt_add = NOW(), price = 7200, user_id = 1, lot_id = 5;
INSERT INTO bets
SET dt_add = NOW(), price = 7100, user_id = 2, lot_id = 5;

/* Запрос существующего списка категорий */
SELECT * FROM cats;

/* Запрос данных самых новых, открытых лотов*/
SELECT name, descr, price, img_url, dt_add, dt_expired FROM lots 
WHERE dt_add > DATE_SUB(NOW(), INTERVAL 2 HOUR) AND dt_expired > NOW();

/* Запрос данных лота по его id и названия его категории*/
SELECT l.name, descr, price, c.name FROM lots l JOIN cats c ON cat_id = c.id WHERE c.code = 'attachment';

/* Обновить название лота по его идентификатору*/
UPDATE lots SET name = 'КРЕПЛЕНИЯ МУЖСКИЕ ЧЕРНО-СЕРЫЕ ILLUSION 700 DREAMSCAPE' WHERE id = 3;

/* Получение списка ставок для лота по его идентификатору с сортировкой по дате*/
SELECT l.name, b.dt_add, b.price, u.name FROM bets b 
JOIN lots l ON lot_id = l.id 
JOIN users u ON b.user_id = u.id ORDER BY b.dt_add ASC;
