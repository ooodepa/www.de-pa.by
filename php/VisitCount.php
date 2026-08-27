<?php

class VisitCount {
    static function updateCount() {
        $uri = $_SERVER['REQUEST_URI'] ?? '-';
        $count = 1;
        $datetime = date('Y-m-d_H:i:s');

        $HOME = strlen($_SERVER['DOCUMENT_ROOT']) != 0 ? $_SERVER['DOCUMENT_ROOT'] : "";

        $pdo = new PDO("sqlite:$HOME/../_visit_count_database.sqlite");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // $sql = "CREATE TABLE IF NOT EXISTS DP_LST_VisitCount (
        //             uri TEXT,
        //             count INT,
        //             created_at DATETIME,
        //             updated_at DATETIME
        //         )
        //         ";

        // $sth = $pdo->prepare($sql);
        // $sth->execute();

        $sql = "SELECT
                    *
                FROM
                    DP_LST_VisitCount
                WHERE
                    uri = :uri
                ";

        $sth = $pdo->prepare($sql);
        $sth->execute([
            'uri' => $uri,
        ]);
        $result = $sth->fetch(PDO::FETCH_ASSOC);

        $isFound = $result != null;

        if ($isFound) {
            $count = $result['count'] + 1;

            $sql = "UPDATE
                        DP_LST_VisitCount
                    SET
                        count = :count,
                        updated_at = :datetime
                    WHERE
                        uri = :uri
                    ";

            $sth = $pdo->prepare($sql);
            $sth->execute([
                'uri' => $uri,
                'count' => $count,
                'datetime' => $datetime,
            ]);
        }

        if (!$isFound) {
            $sql = "INSERT INTO
                        DP_LST_VisitCount
                        (uri, count, created_at, updated_at)
                    VALUES
                        (:uri, :count, :datetime, :datetime)
                    ";

            $sth = $pdo->prepare($sql);
            $sth->execute([
                'uri' => $uri,
                'count' => $count,
                'datetime' => $datetime,
            ]);
        }
    }
}
