<?php

namespace frontend\controllers;

use common\models\Author;
use common\models\AuthorSearch;
use common\models\BookSearch;
use common\models\Sub;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * AuthorController implements the CRUD actions for Author model.
 */
class AuthorController extends Controller
{
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
                    'only' => ['create', 'update', 'delete'],
                    'rules' => [
                        [
                            'actions' => ['sub'],
                            'allow' => true,
                            'roles' => ['?'],
                        ],
                        [
                            'actions' => ['create', 'update','delete'],
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
     * Lists all Author models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AuthorSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Author model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $bookSearchModel = new BookSearch();
        $bookProvider = $bookSearchModel->search(Yii::$app->request->queryParams, author_id: $id);
        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'bookProvider' => $bookProvider,
        ]);
    }

    /**
     * Creates a new Author model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Author();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
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
     * Updates an existing Author model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Author model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Author model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Author the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Author::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Находит записи которые содержат указанную строку и ограничивает
     * количество одновременно отображаемых элементов
     * 
     * @param string $term искомая строка
     * @param string $page номер страницы
     * @param string $limit количество элементов на странице
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionList($term = null, $page = 1, $limit = 20)
    {
        if (Yii::$app->request->isAjax) {
            $out = ['more' => false, 'results' => []];
            $query = Author::find();
            $data = $query
                ->select([
                    'id' => '[[id]]',
                    'text' => '[[fio]]',
                ])
                ->andFilterWhere(['like', '[[fio]]', $term])
                ->orderBy(['fio' => SORT_ASC])
                ->groupBy('id')
                ->limit($limit + 1)
                ->offset(($page - 1) * $limit)
                ->asArray()
                ->all();
            if (count($data) === $limit + 1) {
                $out['more'] = true;
                array_pop($data);
            }
            $out['results'] = $data;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $out;
        }
        throw new ForbiddenHttpException;
    }

    /**
     * Действие на вывод топ 10 авторов по количеству выпущенных книг за определенный год
     * @return string
     */
    public function actionTop()
    {
        $year = Yii::$app->request->get('year', date('Y'));

        $authors = Author::findTopAuthors($year)->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $authors,
        ]);

        return $this->render('top-authors', [
            'dataProvider' => $dataProvider,
            'year' => $year,
        ]);
    }

    /**
     * Подписка на автора
     * @param int $author_id индекс автора
     * @return string|\yii\web\Response
     */
    public function actionSub($author_id)
    {
        $model = new Sub();
        $model->author_id = $author_id;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Подписка успешно оформлена');
                return $this->redirect(['view', 'id' => $model->author->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('sub_form', [
            'model' => $model,
        ]);
    }
}
