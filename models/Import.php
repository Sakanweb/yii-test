<?php

namespace app\models;


use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the upload form.
 */
class Import extends Model
{
    /**
     * @var UploadedFile file attribute
     */
    public $importFile;
    public $storeName;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['storeName', 'importFile'], 'required'],
            //['importFile', 'file'],
            //['importFile', 'file', 'skipOnEmpty' => false, 'extensions'=>['csv'], 'checkExtensionByMimeType'=>false, 'maxFiles' => 1, 'maxSize' => 5242880 ],
            [['importFile'], 'file', 'extensions'=>['csv'], 'checkExtensionByMimeType' => false, 'maxSize' => 5*1024*1024],
            [['storeName'], 'string', 'max' => 255],
        ];
    }

    /**
     * Upload
     * @return array|false
     */
    public function upload()
    {
        $transaction = Yii::$app->db->beginTransaction ();
        try {
            if ($this->load(Yii::$app->request->post(), '')) {
                $this->importFile = UploadedFile::getInstance($this, 'importFile');

                if ($this->importFile && $this->validate()) {
                    $this->storeName = Yii::$app->request->post('storeName');
                    $dataNew = [];
                    //Checking File type is csv
                    if ($this->importFile->extension == "csv") {
                        if (($handle = fopen($this->importFile->tempName, "r")) !== FALSE) {
                            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                                $dataNew[] = $data;
                            }
                            fclose($handle);
                        }
                    }
                    //More file type checking and get content data write here

//                    //Data is empty
//                    if(empty($dataNew)) {
//                        throw new \Exception(json_encode(["importFile" => ["File content cannot be blank."]]));
//                    }

                    $response = Yii::$app->dataParser->init($this->storeName, $dataNew);
                    $transaction->commit();
                    return [
                        'httpStatus' => 200,
                        'data' => "Success"
                    ];
                }
                throw new \Exception(json_encode($this->errors));
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $ex->getMessage()];
        }
    }
}