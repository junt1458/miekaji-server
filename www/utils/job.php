<?php
    include_once __DIR__ . '/profile.php';
    class JobUtil extends ProfileUtil {

        /**
         * ランダム文字列生成 (数字)
         * $length: 生成する文字数
         */
        function makeRandInt($length) {
            $str = range('0', '9');
            $r_str = null;
            for ($i = 0; $i < $length; $i++) {
                $r_str .= $str[rand(0, count($str) - 1)];
            }
            return $r_str;
        }

        function generateCategoryID() {
            $link = $this->connect();
            do {
                $uid = $this->makeRandInt(12);
                $count = mysqli_num_rows(mysqli_query($link, "SELECT category_id FROM JobCategoryTable WHERE category_id=" . $uid));
            } while($count != 0);
            mysqli_close($link);
            return $uid;
        }

        function createJobCategory($name, $weight, $detail) {
            $cid = $this->generateCategoryID();
            $link = $this->connect();
            mysqli_query($link, "INSERT INTO JobCategoryTable (category_id, screen_name, job_weight, detail, isActive) VALUES (" . $cid . ", '" . mysqli_real_escape_string($link, $name) . "', " . mysqli_real_escape_string($link, $weight) . ", '" . mysqli_real_escape_string($link, $detail) . "', true);");
            mysqli_close($link);
        }

        function isCategoryFound($id) {
            $link = $this->connect();
            $count = mysqli_num_rows(mysqli_query($link, "SELECT category_id FROM JobCategoryTable WHERE isActive=true AND category_id=" . mysqli_real_escape_string($link, $id)));
            mysqli_close($link);
            return ($count != 0);
        }

        function removeJobCategory($id) {
            $cid = $this->generateCategoryID();
            if(!$this->isCategoryFound($id)) {
                return false;
            }

            $link = $this->connect();
            mysqli_query($link, "UPDATE JobCategoryTable SET isActive=false WHERE category_id=" . mysqli_real_escape_string($link, $id) . ";");
            mysqli_close($link);
            return true;
        }

        function listJobCategory() {
            $link = $this->connect();
            $query = mysqli_query($link, "SELECT * FROM JobCategoryTable WHERE isActive=true;");
            if(!$query) {
                mysqli_close($link);
                return array();
            }

            $categories = array();
            while ($row = mysqli_fetch_assoc($query)) {
                array_push($categories, array(
                    "ID"=>intval($row["category_id"]),
                    "name"=>$row["screen_name"],
                    "weight"=>floatval($row["job_weight"]),
                    "detail"=>$row["detail"]
                ));
            }
            mysqli_free_result($query);
            mysqli_close($link);

            return $categories;
        }

    }