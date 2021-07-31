<?php
namespace Yeticave;

require_once 'Database.php';

/**
 * Repository класс-расширение класса Database для работы с проектом Yeticave
 */
class Repository extends Database
{
    /**
     * Получить перечень категорий аукциона
     * @param ничего
     * 
     * @return array массив категорий товаров
     */
    public function getAllCategories()
    {
        $cats = array();
        if ($this->isOk()) {
            $sql = "SELECT id, name, code FROM cats";
            $result = $this->query($sql);
            if ($this->isOk() and mysqli_num_rows($result) > 0) {
                $cats = mysqli_fetch_all($result, MYSQLI_ASSOC);
            }
        }
        return $cats;
    }

    /**
     * Получить все активные лоты аукциона
     * @param ничего
     * 
     * @return array массив лотов аукциона с действущей датой, 
     * отсортированных в порядке уменьшения даты объявления лота
     */
    public function getAllLots()
    {
        $lots = array();
        if ($this->isOk()) {
            $sql = "SELECT l.id, l.name, descr, price, img_url, dt_add, dt_expired, c.name as cat_name, cat_id" .
            " FROM lots l JOIN cats c ON c.id = l.cat_id WHERE dt_expired > NOW() ORDER BY dt_add DESC";
            $result = $this->query($sql);
            if ($this->isOk() and mysqli_num_rows($result) > 0) {
                $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
            }
        }
        return $lots;
    }

    /**
     * Получить активные лоты аукциона в соответствии с пагинацией
     * либо независимо от категории либо заданной категории
     * @param int $limit максимальное количество лотов в выборке
     * @param int $offset смещение выборки от начала массива лотов
     * @param int $catId желемая категория лотов выборки
     * 
     * @return array массив лотов аукциона с действущей датой
     * в соответствии с условиями выборки, 
     * отсортированных в порядке уменьшения даты объявления лота
     */
    public function getLots($limit, $offset, $catId = 0)
    {
        $lots = array();
        if ($this->isOk()) {
            $sql = "SELECT l.id, l.name, descr, price, img_url, dt_add, dt_expired, c.name as cat_name," .
            " cat_id, (SELECT COUNT(b.id) FROM bets b WHERE b.lot_id = l.id) AS bets_count" .
            " FROM lots l JOIN cats c ON c.id = l.cat_id WHERE dt_expired > NOW()";
            if ($catId > 0) {
                $sql .= " AND l.cat_id = $catId";
            }
            $sql .= " ORDER BY dt_add DESC LIMIT $limit OFFSET $offset";
            $result = $this->query($sql);
            if ($this->isOk() and mysqli_num_rows($result) > 0) {
                $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
            }
        }
        return $lots;
    }

    /**
     * Добавить в БД новый лот
     * @param array $lot массив с описанием полей лота
     * @param int $authorId id пользователя, заявившего лот
     * 
     * @return true, если запрос добавления выполнен, false в противном случае
     */
    public function addNewLot($lot, $authorId)
    {
        if ($this->isOk()) {
            //запишем данные лота в базу
            $sql = "INSERT INTO lots (dt_add, name, descr, img_url, price, dt_expired, bet_step, cat_id, author_id)" .
            " VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)";
            $data = [$lot['lot-name'], $lot['message'], 'uploads/' . $lot['new-img'], $lot['lot-rate'],
            $lot['lot-date'], $lot['lot-step'], $lot['category'], $authorId];
            $stmt = $this->prepare($sql, $data);
            if ($this->isOk()) {
                $result = mysqli_stmt_execute($stmt);
                return $result;
            }
        }
        return false;
    }

    /**
     * Найти похожие лоты, используя полнотекстовый поиск по двум полям, по запросу
     * @param string $field1 имя первого поля полнотекстового поиска
     * @param string $field2 имя второго поля полнотекстового поиска
     * @param string $whatToSearch запрос поиска
     * 
     * @return array массив лотов аукциона с действущей датой в соответствии с
     * поисковым запросом, отсортированных в порядке уменьшения даты объявления лота
     */
    public function findSimilarLots($field1, $field2, $whatToSearch)
    {
        $lots = array();
        $field1 = "l." . $field1;
        $field2 = "l." . $field2;
        $whatToSearch = "'" . $whatToSearch . "'";
        $sql = "SELECT l.id, l.name, descr, price, img_url, dt_add, dt_expired, c.name as cat_name, cat_id, " .
        " (SELECT COUNT(b.id) FROM bets b WHERE b.lot_id = l.id) AS bets_count" .
        " FROM lots l JOIN cats c ON c.id = l.cat_id WHERE MATCH(" . "$field1" . "," . "$field2" . ")" .
        " AGAINST ($whatToSearch IN BOOLEAN MODE) " .
        "AND dt_expired > NOW() ORDER BY dt_add DESC";
        $result = $this->query($sql);
        if ($this->isOk() and mysqli_num_rows($result) > 0) {
            $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        return $lots;
    }

    /**
     * Получить количество активных лотов
     * @param ничего
     * 
     * @return int количество лотов с действующей датой
     */
    public function getLotsCount()
    {
        if ($this->isOk()) {
            $sql = "SELECT COUNT(*) AS count FROM lots WHERE dt_expired > NOW()";
            $result = $this->query($sql);
            if ($this->isOk() and mysqli_num_rows($result) > 0) {
                return mysqli_fetch_assoc($result)['count'];
            }
        }
        return 0;
    }

    /**
     * Получить данные лота по его id
     * @param int $lotId id лота
     * @param boolean $expired false, если требуется активный лот
     * true, если нужен лот с любой датой окончания торгов
     * 
     * @return array данные лота
     * false, если лот с таким id и датой не найден
     */
    public function getLot($lotId, $expired = false)
    {
        $lot = array();
        if ($this->isOk()) {
            $sql = "SELECT l.id, l.name, descr, price, img_url, dt_add, dt_expired, bet_step, c.name as cat_name," .
            " cat_id, author_id, (SELECT COUNT(b.id) FROM bets b WHERE b.lot_id = l.id) AS bets_count" .
            " FROM lots l JOIN cats c ON c.id = l.cat_id WHERE l.id = $lotId";
            if (!$expired) {
                $sql .= " AND dt_expired > NOW()";
            }
            $result = $this->query($sql);
            if ($this->isOk() and mysqli_num_rows($result) > 0) {
                $lot = mysqli_fetch_assoc($result);
                return $lot;
            }
        }
        $this->setError('Lot id ' . $lotId . ' not found');
        return false;
    }

    /**
     * Получить название категории по ее id
     * @param int $catId id категории
     * 
     * @return array данные найденной категории
     * false, если категории с таким именем не найдено
     */
    public function getCat($catId)
    {
        if ($this->isOk()) {
            $sql = "SELECT id, name FROM cats WHERE id = $catId";
            $result = $this->query($sql);
            if ($this->isOk() and mysqli_num_rows($result) > 0) {
                $cat = mysqli_fetch_assoc($result);
                return $cat;
            }
        }
        $this->setError('Category id ' . $catId . ' not found');
        return false;
    }

    /**
     * Получить id категории по ее названию
     * @param string $catName имя категории
     * 
     * @return int id найденной категории
     * false, если категории с таким именем не найдено
     */
    public function getCatId($catName)
    {
        if ($this->isOk()) {
            $sql = "SELECT id, name FROM cats WHERE name = " . "'" . $catName . "'";
            $result = $this->query($sql);
            if ($this->isOk() and mysqli_num_rows($result) > 0) {
                $cat = mysqli_fetch_assoc($result);
                return $cat['id'];
            }
        }
        $this->setError('Category ' . $catName . ' not found');
        return false;
    }

    /**
     * Получить величину максимальной ставки заданного лота
     * @param int $lotId id лота
     * 
     * @return array данные максимальной ставки для лота,
     * если ставок еще нет, возвращается ставка с нулевым значением цены
     */
    public function getMaxBet($lotId)
    {
        $bet = [
            'price' => 0,
            'author' => ''
        ];
        $sql = "SELECT price, user_id as author FROM bets WHERE lot_id = $lotId ORDER BY price DESC LIMIT 1";
        $result = $this->query($sql);
        if ($this->isOk()) {
            if (mysqli_num_rows($result) > 0) {
                $bet = mysqli_fetch_assoc($result);
            }
        }
        return $bet;
    }

    /**
     * Добавить в БД новую ставку для заданного лота
     * @param array $bet параметры новой ставки для лота
     * @param int $lotId id лота
     * @param int $userId id пользователя, сделавшего ставку
     * 
     * @return true если ставка добавлена успешно, false в противном случае
     */
    public function addNewBet($bet, $lotId, $userId)
    {
        if ($this->isOk()) {
            //запишем данные ставки в базу
            $sql = "INSERT INTO bets (dt_add, price, user_id, lot_id)" .
            " VALUES (NOW(), ?, ?, ?)";
            $data = [$bet['cost'], $userId, $lotId];
            $stmt = $this->prepare($sql, $data);
            if ($this->isOk()) {
                $result = mysqli_stmt_execute($stmt);
                return $result;
            }
        }
        return false;
    }

    /**
     * Получить перечень ставок для заданного лота
     * @param int $lotId id лота
     * 
     * @return array массив с данными ставок лота
     */
    public function getBetHistory($lotId)
    {
        $betHistory = array();
        $sql = "SELECT price, dt_add, u.name as name FROM bets JOIN users u ON u.id = user_id" .
        " WHERE lot_id = $lotId ORDER BY dt_add DESC";
        $result = $this->query($sql);
        if ($this->isOk() and mysqli_num_rows($result) > 0) {
            $betHistory = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        return $betHistory;
    }

    /**
     * Получить перечень ставок заданного пользователя
     * @param int $userId id пользователя
     * 
     * @return array массив с данными ставок пользователя
 */
    public function getUserBets($userId)
    {
        $bets = array();
        $sql = "SELECT b.user_id, b.price, b.dt_add, b.lot_id, l.name as lot_name," .
        " l.img_url, l.dt_expired, l.winner_id, u.contact, c.name as cat_name" .
        " FROM bets b JOIN lots l ON l.id = b.lot_id JOIN cats c ON c.id = l.cat_id" .
        " JOIN users u ON u.id = $userId WHERE b.user_id = $userId" .
        " ORDER BY dt_add DESC";
        $result = $this->query($sql);
        if ($this->isOk() and mysqli_num_rows($result) > 0) {
            $bets = mysqli_fetch_all($result, MYSQLI_ASSOC);
            //если есть несколько ставок на один лот, выберем бОльшую ставку
            $bets = array_filter($bets, function ($bet) {
                $lotId = $bet['lot_id'];
                $userId = $bet['user_id'];
                //выберем наибольшую стоимость среди ставок
                $sql = "SELECT price FROM bets WHERE lot_id = $lotId AND user_id = $userId ORDER BY price DESC LIMIT 1";
                $result = $this->query($sql);
                if ($this->isOk() and mysqli_num_rows($result) > 0) {
                    $price = mysqli_fetch_assoc($result)['price'];
                    //ставка с наибольшей стоимостью
                    if ($price === $bet['price']) {
                        return true;
                    }
                }
                return false;
            });
        }
        return $bets;
    }

    /**
     * Получить данные пользователя по его электронной почте
     * @param string $email адрес электронной почты пользователя
     * 
     * @return array данные пользователя, если он найден
     * false в противном случае
     */
    public function getUser($email)
    {
        if ($this->isOk()) {
            $sql = "SELECT id, name, email FROM users WHERE email = " . "'" . $email . "'";
            $result = $this->query($sql);
            if ($this->isOk() and mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
                return $user;
            }
            $this->setError('email ' . $email . ' not found');
        }
        return false;
    }

    /**
     * Получить данные пользователя по id
     * @param int $userId id пользователя
     * 
     * @return array данные пользователя, если он найден
     * false в противном случае
     */
    public function getUserById($userId)
    {
        if ($this->isOk()) {
            $sql = "SELECT id, name, email FROM users WHERE id = $userId";
            $result = $this->query($sql);
            if ($this->isOk() and mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
                return $user;
            }
            $this->setError('user with id ' . $userId . ' not found');
        }
        return false;
    }
    
    /**
     * Зарегистрировать нового пользователя
     * @param array $user данные пользователя
     * 
     * @return true если пользователь зарегистрирован,
     * false в противном случае
     */
    public function registerNewUser($user)
    {
        if ($this->isOk()) {
            //запишем данные юзера в базу
            $passwordHash = password_hash($user['password'], PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (dt_reg, name, email, password, contact)" .
            " VALUES (NOW(), ?, ?, ?, ?)";
            $data = [$user['name'], $user['email'], $passwordHash, $user['message']];
            $stmt = $this->prepare($sql, $data);
            if ($this->isOk()) {
                $result = mysqli_stmt_execute($stmt);
                return $result;
            }
        }
        return false;
    }

    /**
     * Получить кэшированный пароль пользоватеоя по его id
     * @param int id пользователя
     * 
     * @return string кэшированный пароль, если пользователь найден,
     * false в противном случае
     */
    public function getUserPwd($userId)
    {
        if ($this->isOk()) {
            $sql = "SELECT password, id FROM users WHERE id = $userId";
            $result = $this->query($sql);
            if ($this->isOk() and mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
                return $user['password'];
            }
            $this->setError('Пользователь id= ' . $userId . ' не найден');
        }
        return false;
    }

    /**
     * Определить пользователей-победителей аукциона
     * @param ничего
     * 
     * @return array массив победителей [id-победителя, id-лота] 
     */
    public function defineWinners()
    {
        $winners = array();
        if ($this->isOk()) {
            $sql = "SELECT id FROM lots WHERE (winner_id IS NULL OR winner_id = 0) AND dt_expired < NOW()";
            $result = $this->query($sql);
            if ($this->isOk() and mysqli_num_rows($result) > 0) {
                $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
                foreach ($lots as $lot) {
                    if ($this->isOk()) {
                        $lotId = $lot['id'];
                        $maxBet = $this->getMaxBet($lotId);
                        if (isset($maxBet['author'])) {
                            $winnerId = $maxBet['author'];
                            $sql = "UPDATE lots SET winner_id = $winnerId WHERE id = $lotId";
                            $this->query($sql);
                            $winners[] = [
                                'id' => $winnerId,
                                'lotId' => $lotId
                            ];
                        }
                    } else {
                        return false;
                    }
                }
                return $winners;
            }
        }
        return false;
    }
}
