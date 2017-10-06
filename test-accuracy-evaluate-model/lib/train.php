<?php

/**
 * @author Adriano Maciel
 * @github https://github.com/adrianowead
 */
class FannTrain{
  private $dataCsv = [];
  private $x_dataset = [];
  private $y_dataset = [];

  // train dataset
  private $x_dataset_test = [];
  private $y_dataset_test = [];

  private $fannDataSetFile = '';

  public $neurons_each_layer = [5];
  public $dropout = 0.0001;
  public $number_epochs = 1;
  public $batch_size = 100;
  public $activaction_function_hidden = FANN_SIGMOID_SYMMETRIC;
  public $activaction_function_output = FANN_SIGMOID_SYMMETRIC;
  public $portion_test_data = 0; // by default, all dataset is used and no have train separated data

  public $verbose = 0;

  private $lastAccuracy = 0;

  public function __construct(){
    //
  }

  /**
   * Load csv dataset
   */
  public function loadDataSetCSV( string $file, string $separator ){

    if( $this->verbose == 2 )
    {
      echo date("d/m/y H:i:s") . " - Loading data from csv file..." . chr(13).chr(10);
    }

    $handler = fopen($file, "rb");

    while( $csv = fgetcsv($handler, 0, ",") )
    {
      $this->dataCsv[] = $csv;
    }

    // this shuffle is only to simulate other data with another order
    // because the the result is different depending what rows is trained and what not
    //shuffle($this->dataCsv);

    if( $this->verbose == 2 )
    {
      echo date("d/m/y H:i:s") . " - Formating dataset..." . chr(13).chr(10);
    }

    // split on X dataset (input)
    $this->x_dataset = array_map(function($i){
      array_pop($i);
      return $i;
    }, $this->dataCsv);

    // split on Y dataset (output)
    $this->y_dataset = array_map(function($i){
      return array_pop($i);
    }, $this->dataCsv);

    // if have portion to accuracy test
    if( $this->portion_test_data > 0 && $this->portion_test_data < 1 )
    {
      if( $this->verbose == 2 )
      {
        echo date("d/m/y H:i:s") . " - Loading dataset to performance test..." . chr(13).chr(10);
      }

      $portion = ceil( sizeof( $this->x_dataset ) * $this->portion_test_data );

      if( $portion > 0 )
      {
        // extracting
        $this->x_dataset_test = array_splice( $this->x_dataset, 0, $portion );
        $this->y_dataset_test = array_splice( $this->y_dataset, 0, $portion );
      }
    }


    // normalize all datasets
    $this->x_dataset = $this->normalizeDataset( $this->x_dataset );

    if( $this->verbose == 2 )
    {
      echo date("d/m/y H:i:s") . " - Loaded data: " . chr(13).chr(10);
      echo "                         - Total registers: " . sizeof( $this->dataCsv ) . chr(13).chr(10);
      echo "                         - For training: " . sizeof( $this->x_dataset ) . chr(13).chr(10);
      echo "                         - For performance (accuracy) test: " . sizeof( $this->x_dataset_test ) . chr(13).chr(10);
      echo chr(13).chr(10);
    }
  }

  /**
   * Create dataset file to train FANN
   */
  public function createFannDataSet( string $target ){

    if( $this->verbose == 2 )
    {
      echo date("d/m/y H:i:s") . " - Creating FANN train dataset file..." . chr(13).chr(10);
    }

    // criando os dados de saida para um arquivo
    $file = fopen($target, "wb");

    fwrite($file, implode(" ", [ sizeof( $this->x_dataset ), sizeof( $this->x_dataset[0] ), sizeof($this->y_dataset[0]) ])  );

    foreach( $this->x_dataset as $k => $v ){
      fwrite($file, chr(13).chr(10) );
      fwrite($file, implode(" ", $v) . chr(13).chr(10) );
      fwrite($file, $this->y_dataset[$k] );
    }

    fclose( $file );

    $this->fannDataSetFile = $target;

    if( $this->verbose == 2 )
    {
      echo date("d/m/y H:i:s") . " - Created FANN dataset file structure to train ({$this->fannDataSetFile})" . chr(13).chr(10);
      echo chr(13).chr(10);
    }
  }

  /**
   * Execute network train
   */
  public function trainNetwork( string $fannOutPut ){

    if( $this->verbose == 2 )
    {
      echo date("d/m/y H:i:s") . " - Creating neural network and start training..." . chr(13).chr(10);
    }

    // config network
    $num_input = sizeof( $this->x_dataset[0] );
    $num_output = sizeof( $this->y_dataset[0] );
    $desired_error = $this->dropout;
    $max_epochs = $this->number_epochs;
    $epochs_between_reports = $this->batch_size;

    // adjust array to set FANN params
    array_push( $this->neurons_each_layer, $num_output );

    // input
    array_unshift( $this->neurons_each_layer, $num_input );

    // num args (layers)
    $num_layers = sizeof( $this->neurons_each_layer );

    $args = $this->neurons_each_layer;
    array_unshift( $args, $num_layers );

    // creating fann
    $this->my_ann = call_user_func_array('fann_create_standard', $args);

    if($this->my_ann){
      fann_set_activation_function_hidden($this->my_ann, $this->activaction_function_hidden);
      fann_set_activation_function_output($this->my_ann, $this->activaction_function_output);

      // set callback
      fann_set_callback($this->my_ann, [$this, "testBatch"]);

      if (fann_train_on_file($this->my_ann, $this->fannDataSetFile, $max_epochs, $epochs_between_reports, $desired_error))
        fann_save($this->my_ann, $fannOutPut);

      if( $this->verbose == 2 )
      {
        echo date("d/m/y H:i:s") . " - Network trained ({$fannOutPut})" . chr(13).chr(10);
        echo chr(13).chr(10);
      }
    }
  }

  /**
   * Test accuracy for current batch
   */
  private function testBatch( $ann, $train, $max_epochs, $epochs_between_reports, $desired_error, $epoch ){

    if( $this->verbose >= 1 )
    {
      $accuracy = 0;

      foreach( $this->x_dataset_test as $k => $v ){
        $predict = $this->runTest( $v );

        if( $predict == $this->y_dataset_test[$k] )
          $accuracy++;
      }

      $accuracy = ( $accuracy / sizeof( $this->x_dataset_test ) ) * 100;
      $out = number_format( $accuracy, 2, '.', '' ) . "%";

      if( $this->lastAccuracy != $out )
      {
        $this->lastAccuracy = $out;

        echo "Epoch: {$epoch} -> accuracy: {$out}" . chr(13);
      }
    }

    // need to return true to continue trainig until next batch
    // or false to stop here
    return true;
  }

  // load a previously trained network
  public function loadTrainedNetwok( string $source ){
    $this->my_ann = fann_create_from_file( $source );
  }

  // function to run only one input
  public function runTest( array $input ){
    $predict = fann_run( $this->my_ann, $input );

    return round( $predict[0] );
  }


  public function testNetworkAccuracy(){
    $accuracy = 0;

    if( sizeof( $this->x_dataset_test ) > 0 )
    {
      foreach( $this->x_dataset_test as $k => $v ){

        $predict = fann_run( $this->my_ann, $v );

        if( round( $predict[0] ) == $this->y_dataset_test[$k] )
          $accuracy++;
      }

      $accuracy = ( $accuracy / sizeof( $this->x_dataset_test ) ) * 100;
      $accuracy = number_format( $accuracy, 2, '.', '' ) . "%";
    }
    else
    {
      $accuracy = "100%";
    }

    echo chr(13).chr(10);
    echo chr(13).chr(10);
    echo "Mean Square Error (MSE): " . fann_get_MSE( $this->my_ann ) . chr(13).chr(10);
    echo "Total Neurons: " . fann_get_total_neurons( $this->my_ann ) . chr(13).chr(10);
    echo "Total Connections: " . fann_get_total_connections( $this->my_ann ) . chr(13).chr(10);
    echo "Accuracy rate (with test data): {$accuracy}" . chr(13).chr(10);
    echo chr(13).chr(10);
  }

  /**
   * This function convert non-numbers to numbers
   * Numbers required for FANN
   */
  private function normalizeDataset( $dataset ){
    $out = $dataset;

    // convert all dataset to numbers
    // if needed
    foreach( $out as $k => $v ){
      if( is_array( $v ) )
      {
        $out[$k] = $this->normalizeDataset( $v );
      }
      else if( !is_numeric( $v ) )
      {
        $v = (string)$v;

        $calc = 0;

        for( $y = 0; $y < strlen($v); $y++ ){
          // using ord char
          // because if on untrained input has same text
          // it should be have the same representation
          $calc .= ord( $v[$y] );
        }

        $out[$k] = $calc;
      }
    }

    return $out;
  }
}

?>