<?php

	class ArticleController extends Controller
	{
		/**
		 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
		 * using two-column layout. See 'protected/views/layouts/column2.php'.
		 */
		public $layout = '//layouts/column1';
		public $defaultAction = 'admin';

		/**
		 * @return array action filters
		 */
		public function filters()
		{
			return array(
				'accessControl', // perform access control for CRUD operations
				'postOnly + delete', // we only allow deletion via POST request
			);
		}

		/**
		 * Specifies the access control rules.
		 * This method is used by the 'accessControl' filter.
		 * @return array access control rules
		 */
		public function accessRules()
		{
			return array(
				array('allow', // allow all users to perform 'index' and 'view' actions
					'actions' => array('index', 'view'),
					'users' => array('*'),
				),
				array('allow', // allow authenticated user to perform 'create' and 'update' actions
					'actions' => array('create', 'update'),
					'users' => array('@'),
				),
				array('allow', // allow admin user to perform 'admin' and 'delete' actions
					'actions' => array('admin', 'delete'),
					'users' => array('admin'),
				),
				array('deny', // deny all users
					'users' => array('*'),
				),
			);
		}

		/**
		 * Displays a particular model.
		 * @param integer $id the ID of the model to be displayed
		 */
		public function actionView($id)
		{
			// Getting information about authors
			$authors = array();

			$criteria = new CDbCriteria();
			$criteria->condition = '`node_id` = :id';
			$criteria->params = array(':id' => $id);

			$relations = ArticleAuthors::model()->findAll($criteria);

			foreach ($relations as $one) {
				$criteria = new CDbCriteria();
				$criteria->condition = '`id` = :id';
				$criteria->params = array(':id' => $one->author_id);

				$info['id'] = $one->author_id;

				$author = Profile::model()->find($criteria);

				$info['name'] = $author->name;
				$authors[] = $info;
			}
			$this->render('view', array(
				'model' => $this->loadModel($id),
				'authors' => $authors,
			));
		}

		/**
		 * Creates relation between author and article
		 * @param mixed $id id or name of author
		 * @param id $article_id id of article
		 */
		private function addAuthor($id, $article_id)
		{
			if (is_int($id)) {
				// Author exists
				$relation = new ArticleAuthors;
				$relation->node_id = $article_id;
				$relation->author_id = $id;
				$relation->save() or die($relation->author_id);
			} else {
				// Author doesn't exist

				// Create new profile without user
				$profile = new Profile;
				$profile->user_id = -1;
				$profile->email = '-1';
				$profile->academic = '-1';
				$profile->name = $id;

				$profile->save() or die ("PROFILE 1");

				// Now, author exists. We can create relations
				$relation = new ArticleAuthors;
				$relation->node_id = $article_id;
				$relation->author_id = $profile->id;
				$relation->save() or die($profile->name);
			}
		}

		/**
		 * Creates a new model.
		 * If creation is successful, the browser will be redirected to the 'view' page.
		 */
		public function actionCreate()
		{
			// Setting admin layout
			$this->layout = 'application.modules.admin.views.layouts.admin';

			$model = new Article;
			$advModel = new ArticleAdv;

			// Uncomment the following line if AJAX validation is needed
			// $this->performAjaxValidation($model);

			if (isset($_POST['Article'])) {
				$model->attributes = $_POST['Article'];
				$model->author = Yii::app()->user->id;
				$model->created = new CDbExpression('NOW()');

				$advModel->attributes = $_POST['ArticleAdv'];
				$advModel->node_id = 0;

				if ($model->validate() && $advModel->validate()) {
					$model->save();
					$advModel->node_id = $model->id;
					$advModel->save();

					// Inserting authors
					$authors = json_decode($advModel->aditional_authors, true);

					foreach ($authors as $key => $value) {
						if ($value == 1) {
							// Author will be added to database
							$this->addAuthor($key, $model->id);
						}
					}

					$this->redirect(array('author/article/admin'));
				}
			}

			$this->render('create', array(
				'model' => $model,
				'advModel' => $advModel,
			));
		}

		/**
		 * Updates a particular model.
		 * If update is successful, the browser will be redirected to the 'view' page.
		 * @param integer $id the ID of the model to be updated
		 */
		public function actionUpdate($id)
		{
			// Setting admin layout
			$this->layout = 'application.modules.admin.views.layouts.admin';

			$model = $this->loadModel($id);
			$advModel = ArticleAdv::model()->find('node_id = :id', array(':id' => $id));

			// Getting info about aditional authors
			$criteria = new CDbCriteria();
			$criteria->condition = '`node_id` = :id';
			$criteria->params = array(':id' => $id);

			$relation = ArticleAuthors::model();
			$authors = $relation->findAll($criteria);

			$relations = array();

			foreach ($authors as $element) {
				$author_id = $element->author_id;

				$criteria = new CDbCriteria();
				$criteria->condition = '`id` = :id';
				$criteria->params = array(':id' => $author_id);

				$profile = Profile::model()->find($criteria);

				$relations[$profile->id] = $profile->name;
			}

			$advModel->aditional_authors = json_encode($relations);

			if (isset($_POST['Article'])) {
				$model->attributes = $_POST['Article'];
				$model->updated = new CDbExpression('NOW()');
				$model->updater = Yii::app()->user->id;

				$advModel->attributes = $_POST['ArticleAdv'];
				$advModel->node_id = 0;
				//die($advModel->aditional_authors);

				if ($model->validate() && $advModel->validate()) {
					$model->save();
					$advModel->node_id = $model->id;
					$advModel->save();

					// Save information about aditional authors
					$authors = json_decode($advModel->aditional_authors, true);

					foreach ($authors as $id => $value) {
						if ($value == 0) {
							// Author has been deleted.
							if (is_int($id)) {
								// Author exists in database and may be there is a relation

								$criteria = new CDbCriteria();
								$criteria->condition = '`author_id` = :id AND `node_id` = :node';
								$criteria->params = array(':id' => $id, ':node' => $model->id);

								$relation = ArticleAuthors::model();

								if ($relation->count($criteria) > 0) {
									// There is a relation. Delete it
									$relation->find($criteria)->delete();
								}
							}
							// Else, author doesn't exists. That means,
							// that there is no relations
						} elseif ($value == 1) {
							// Author has been added
							$this->addAuthor($id, $model->id);
						}
					}

					$this->redirect(array('default/index'));
				}
			}

			$this->render('update', array(
				'model' => $model,
				'advModel' => $advModel
			));
		}

		/**
		 * Deletes a particular model.
		 * If deletion is successful, the browser will be redirected to the 'admin' page.
		 * @param integer $id the ID of the model to be deleted
		 */
		public function actionDelete($id)
		{
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if (!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}

		/**
		 * Lists all models.
		 */
		public function actionIndex()
		{
			$dataProvider = new CActiveDataProvider('Article');
			$this->render('index', array(
				'dataProvider' => $dataProvider,
			));
		}

		/**
		 * Manages all models.
		 */
		public function actionAdmin()
		{
			// Setting admin layout
			$this->layout = 'application.modules.admin.views.layouts.admin';

			$model = new Article('search');
			$model->unsetAttributes(); // clear any default values
			if (isset($_GET['Article']))
				$model->attributes = $_GET['Article'];

			$this->render('admin', array(
				'model' => $model,
			));
		}

		/**
		 * Returns the data model based on the primary key given in the GET variable.
		 * If the data model is not found, an HTTP exception will be raised.
		 * @param integer the ID of the model to be loaded
		 */
		public function loadModel($id)
		{
			$model = Article::model()->findByPk($id);
			if ($model === null)
				throw new CHttpException(404, 'The requested page does not exist.');
			return $model;
		}

		/**
		 * Performs the AJAX validation.
		 * @param CModel the model to be validated
		 */
		protected function performAjaxValidation($model)
		{
			if (isset($_POST['ajax']) && $_POST['ajax'] === 'article-form') {
				echo CActiveForm::validate($model);
				Yii::app()->end();
			}
		}
	}
