<?php

namespace frontend\controllers;

use common\models\Book;
use common\models\BookAuthor;
use common\models\BookSearch;
use common\models\File;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;

/**
 * BookController implements the CRUD actions for Book model.
 */
class BookController extends Controller
{
    const DIR = 'uploads/books/';

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'only' => ['create', 'update', 'delete', 'deleteFile'],
                    'rules' => [
                        [
                            'actions' => ['create', 'update','delete', 'deleteFile'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Book models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Book model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Book model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Book();

        if ($this->request->isPost) {
            $file = UploadedFile::getInstance($model, 'uploadedFile');
            if ($file) {
                $filename = time() . '.' . $file->extension;
                $file->saveAs(self::DIR . $filename);
    
                $fileModel = new File();
                $fileModel->filepath = self::DIR . $filename;
                $fileModel->filename = $file->name;
                $fileModel->save();
    
                $model->file_id = $fileModel->id;
            }

            if ($model->load($this->request->post()) && $model->save()) {
                $selectedAuthors = Yii::$app->request->post('Book')['bookAuthorIds'];
                if (!empty($selectedAuthors)) {
                    foreach ($selectedAuthors as $authorId) {
                        $bookAuthor = new BookAuthor();
                        $bookAuthor->book_id = $model->id;
                        $bookAuthor->author_id = $authorId;
                        $bookAuthor->save();
                    }
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Book model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $attachedFile = $model->getFile()->one();
        if ($attachedFile) {
            $model->uploadedFile = $attachedFile->filename;
        }

        if ($this->request->isPost) {
            $file = UploadedFile::getInstance($model, 'uploadedFile');
            if ($file) {
                $filename = time() . '.' . $file->extension;
                $file->saveAs(self::DIR . $filename);

                $fileModel = new File();
                $fileModel->filepath = self::DIR . $filename;
                $fileModel->filename = $file->name;
                $fileModel->save();

                $model->file_id = $fileModel->id;
            }

            if ($model->load($this->request->post()) && $model->save()) {
                $selectedAuthors = Yii::$app->request->post('Book')['bookAuthorIds'];
                $authors = BookAuthor::findAll(['book_id' => $model->id]);
                foreach ($authors as $author) {
                    $author->delete();
                }
                if (!empty($selectedAuthors)) {
                    foreach ($selectedAuthors as $authorId) {
                        $bookAuthor = new BookAuthor();
                        $bookAuthor->book_id = $model->id;
                        $bookAuthor->author_id = $authorId;
                        $bookAuthor->save();
                    }
                }
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Book model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $fileModel = File::findOne($model->file_id);
        if ($fileModel) {
            unlink($fileModel->filepath);
            $fileModel->delete();
        }
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Book model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Book the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /** 
     * Действие на удаление файла
     * 
     * @param int $id индекс payment
     * @param string $file наименование атрибута модели
     * @return string|yii\web\Response
     * @throws ForbiddenHttpException не хватает прав | акт закреплен
     */
    public function actionDeleteFile($id)
    {
        $model = $this->findModel($id);
        $file_path = Yii::$app->basePath . '/web/' . $model->file->filepath;
        if (unlink($file_path)) {
            $model->file->delete();
        }
        return $this->redirect(['update', 'id' => $id]);
    }
}
