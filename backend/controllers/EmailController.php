<?php

namespace backend\controllers;

use Yii;
use backend\models\Email;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EmailController implements the CRUD actions for Email model.
 */
class EmailController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'update-status' => ['GET']
                ],
            ],
        ];
    }

    /**
     * Lists all Email models.
     * @return mixed
     */
    public function actionIndex()
    {
        $params = Yii::$app->getRequest()->getQueryParams();
        $where = [];
        foreach($params as $key => $value) {
            if ($key == "import" || $key == "status" || $key == "export" && is_numeric($value))
                $where[$key] = $value;
            else if ($key == "type") {
                $where[$key] = strtoupper($value);
            }
        }

        if (isset($params["all"]) && $params["all"] == true) {
            $where = ["<>", "status", 1];
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Email::find()->where($where),
            'pagination' => [
                'pageSize' => 500,
            ],
        ]);

        $summay = $this->listOut();
        $title = "Danh sách Emails";

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'title' => $title,
            'summary' => $summay
        ]);
    }

    private function listOut() {
        $yahoo = $this->summary("YAHOO");
        $outlook = $this->summary("OUTLOOK");
        $gmail = $this->summary("GMAIL");
        return [
            "yahoo" => $yahoo,
            "outlook" => $outlook,
            "gmail" => $gmail
        ];
    }

    private function summary($type) {
        $total = Email::find()->where(["type" => $type])->count();
        $success = Email::find()->where(["type" => $type, "status" => 1, "export" => 0])->count();
        $success_2m = Email::find()->where(["type" => $type, "status" => -1, "export" => 0])->count();
        $failed = Email::find()->where(["type" => $type])->andWhere([">", "status", 1])->count();
        $remain = Email::find()->where(["type" => $type, "import" => 0, "status" => 0])->count();
        $export = Email::find()->where(["type" => $type, "status" => 1, "export" => 1])->count();
        return [
            "total" => $total,
            "success" => $success,
            "success_2m" => $success_2m,
            "failed" => $failed,
            "export" => $export,
            "remain" => $remain
        ];
    }


    /**
     * Displays a single Email model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Email model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Email();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Email model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Email model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Email model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Email the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Email::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionUpload() {
        try {
            $lines = [];
            foreach($_FILES as $file) {
                $f = fopen($file["tmp_name"], "r");
                $lines = [];
                while(!feof($f))
                {
                    $l = explode("|", fgets($f));
                    
                    if (count($l) != 2 && count($l) != 3) continue;

                    if (count($l) == 3) {
                        $l[0] = trim(preg_replace('/\s\s+/', ' ', $l[0]));
                        $l[1] = trim(preg_replace('/\s\s+/', ' ', $l[1]));
                        $l[2] = trim(preg_replace('/\s\s+/', ' ', $l[2]));

                        $mail = $l[0] . "|" . $l[1];
                        $pass = $l[2];
                        $l = [];
                        $l[0] = $mail;
                        $l[1] = $pass;
                    } else {
                        $l[0] = trim(preg_replace('/\s\s+/', ' ', $l[0]));
                        $l[1] = trim(preg_replace('/\s\s+/', ' ', $l[1]));
                    }

                    $l[0] = strtolower($l[0]);
                    if (strpos($l[0], 'outlook') !== false) {
                        $type = "OUTLOOK";
                    } else if (strpos($l[0], 'yahoo') !== false) {
                        $type = "YAHOO";
                    } else if (strpos($l[0], 'gmail') !== false) {
                        $type = "GMAIL";
                    } else {
                        $type = "UNKNOW";
                    }
                    $l[] = $type;
                    $lines[] = $l;
                    if (count($lines) == 10000) {
                        $this->insertEmails($lines);
                        $lines = [];
                    }
                }
                if (count($lines) > 0) {
                    $this->insertEmails($lines);
                }
                fclose($f);
            }
            echo json_encode(['status' => true]);die;
        } catch(Exception  $error) {
            echo json_encode(['status' => false]);die;
        }
    }

    private function insertEmails($emails) {
        Yii::$app->db->createCommand()
            ->batchInsert(
                'email',
                ['email', 'password', 'type'],
                $emails
            )
            ->execute();
    }

    public function actionGetEmails() {
        try {
            $result = Email::find()
                ->select(['id', 'email', 'password'])
                ->where(['import' => 0])
                ->limit(2000)
                ->asArray()
                ->all();
    
            $ids = array_map(function($item) {
                return $item["id"];
            }, $result);
            $this->updateTried($ids);
    
            if (empty($result)) {
                $this->resetFailedEmails();
            }
            echo json_encode(["status" => true, "data" => $result]); exit;
        } catch(Exception $e) {
            echo json_encode(["status" => false, "data" => []]); exit;
        }
    }

    public function actionGetNumberOfEmails($count) {
        try {
            if (!isset($count) || empty($count) || !is_numeric($count)) {
                $count = 40;
            }
            $result = Email::find()
                ->select(['id', 'email', 'password'])
                ->where(['import' => 0])
                ->limit($count)
                ->asArray()
                ->all();
    
            $ids = array_map(function($item) {
                return $item["id"];
            }, $result);
            $this->updateTried($ids);
    
            if (empty($result)) {
                $this->resetFailedEmails();
            }
            echo json_encode(["status" => true, "data" => $result]); exit;
        } catch(Exception $e) {
            echo json_encode(["status" => false, "data" => []]); exit;
        }
    }

    private function updateTried($ids) {
        if (!count($ids)) return;
        $where = "id IN (". implode(",", $ids) .")";
        Email::updateAll(['import' => 1], $where);
    }

    private function resetFailedEmails() {
        // Email::updateAll(['import' => 0, 'status' => 0], "status != 1");
        Email::updateAll(['import' => 0, 'status' => 0], "status > 1");
    }

    public function actionUpdateStatus($id, $status) {
        if (!$id || !$status) {
            return;
        }
        if (!is_numeric($id) || !is_numeric($status)) {
            return;
        }

        // Email da dc su dung => đánh dấu thành công
        if ($status == 5 || $status == "5") {
            // $status = 1;
        }
        if ($status == 16 || $status == "16") {
            // $status = 1;
        }

        $dataUpdate = [
            'status' => intval($status)
        ];

        if ($status == 1 || $status == "1") {
            $dataUpdate['import'] = 1;
        }
        $where = "id = " . $id;
        Email::updateAll($dataUpdate, $where);
        echo json_encode(["status" => true, "data" => ""]); exit;
    }

    public function actionDeleteAll() {
        Email::deleteAll('status != 1 && status != -1 && status != -2');
        echo json_encode(["status" => true, "data" => ""]); exit;        
    }

    public function actionDeleteByStatus($status) {
        if (!is_numeric($status) || $status == 1) {
            echo json_encode(["status" => false, "data" => ""]); exit;        
        }

        Email::deleteAll(["status" => $status]);
        echo json_encode(["status" => true, "data" => ""]); exit;        
    }

    public function actionDownloadSuccessFile($amount = 200000, $type = null) {
        $result = $this->getSuccessData($amount, $type);
        if (!$result) return;
        $this->downloadSuccessFile();
    }
    
    private function downloadSuccessFile() {
        $path = $_SERVER['DOCUMENT_ROOT'] . "/success.txt";
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($path));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }

    private function getSuccessData($amount, $type) {
        $ids = [];
        $where = ['status' => 1, 'export' => 0];
        if (!empty($type)) {
            $where['type'] = $type;
        }

        $result = Email::find()
        ->select(['id', 'email', 'password', 'type', 'dob'])
        ->where($where)
        ->limit($amount)
        ->asArray()
        ->all();

        if (empty($result)) {
            echo "KHÔNG CÒN GÌ ĐỂ XUẤT!"; exit;
        }

        $content = "";
        foreach($result as $item) {
            $ids[] = $item["id"];
            $line = "";
            if ($item["type"] == "YAHOO") {
                $splited = explode("|", $item["email"]);
                $line .= $splited[0];
                // if (count($splited) == 2) $line .= $splited[1];
                // else $line .= $splited[0];
            } else {
                $line .= $item["email"];
            }
            $line .= "|Zxcv123123";
            if (isset($item["dob"]) && !empty($item["dob"]) && $item["dob"] != null) {
                if ($item["dob"] != "USED") {
                    $line .= "|" . $item["dob"];
                }
            }
            $content .= $line . "\r\n";
        }

        try {
            $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/success.txt","wb");
            fwrite($fp,$content);
            fclose($fp);
            $this->updateExportedEmail($ids);
        } catch (Exception $e) {
            
        }
        return true;
    }

    private function updateExportedEmail($ids) {
        $where = "id IN (". implode(",", $ids) .")";
        Email::updateAll(['export' => 1], $where);
    }

    public function actionForceReset() {
        Email::updateAll(['import' => 0, 'status' => 0], "status > 1");
    }

    public function actionUpdateSuccess($id, $dob) {
        if (!$id || !$dob) {
            return;
        }
        if (!is_numeric($id)) {
            return;
        }

        $where = "id = " . $id;
        Email::updateAll([
            'status' => 1,
            'import' => 1,
            'dob' => $dob
        ], $where);
        echo json_encode(["status" => true, "data" => ""]); exit;
    }

    public function actionUpdateSuccess2m($id, $dob) {
        if (!$id || !$dob) {
            return;
        }
        if (!is_numeric($id)) {
            return;
        }

        $where = "id = " . $id;
        Email::updateAll([
            'status' => -1,
            'import' => 1,
            'dob' => $dob
        ], $where);
        echo json_encode(["status" => true, "data" => ""]); exit;
    }
}
