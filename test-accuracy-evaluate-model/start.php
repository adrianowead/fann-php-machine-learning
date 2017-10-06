<?php

/**
 * Usando a biblioteca FANN
 * Para teste de predicao se um paciente tem ou nao diabetes, com base nos dados diagnosticos
 *
 * Fonte: https://www.kaggle.com/uciml/pima-indians-diabetes-database
 *
 * @author Adriano Maciel
 * @github https://github.com/adrianowead
 */

require_once "lib/train.php";


$startTime = microtime(true);


$ann = new FannTrain;

// definindo configuracoes da rede
$ann->neurons_each_layer = [
  256
]; // cada item do array, é uma nova camada de neurônios ocultos (sem contar os de entrada e saida, gerados com base nos dados informados)
   // camadas sequenciais com a mesma quantidade, são chamadas de "full-connected"
   // não adicione muitas camadas, e naõ coloque muitos neuronios por camada
   // o tempo de processo aumentara exponencialmente
   // mais camadas aumenta a abstracao pela rede, mas a utilidade depende do problema
   // geralmente uma ou duas camadas com mais neuronios tem melhor desempenho

$ann->number_epochs = 5000; // quantidade de geracoes a serem treinadas

$ann->dropout = 0.0001; // quanto menor, maior sera a precisao da rede, porem demanda mais processamento

$ann->batch_size = 100; // a cada quantos epochs sera executado o teste de desempenho, um valor muito baixo aumenta a frequencia de testes, deixando mais lento
                        // coloque cerca de 10% da quantidade de epochs, assim a cada 10% do treinamento sera atualizada a informacao da precisao de cada epoch

// a funcao FANN_ELLIOT, é similar a FANN_SIGMOID_SYMMETRIC, mas mais rápida
// porém tem uma pequena perda de precisao neste teste

// $ann->funcao_ativacao_hidden = FANN_ELLIOT; // a funcao de ativacao selecionada, modifica o tempo de treinamento e a taxa de acerto
// $ann->funcao_ativacao_output = FANN_ELLIOT;

$ann->verbose = 1; // 0 = no verbose during train
                   // 1 = only epoch and accuracy during train
                   // 2 = full verbose

$ann->portion_test_data = 0.3; // entre 0 e 1, parte dos dados que nao serao treinados, e sim usados como simulação do "mundo real", para medir a precisão da rede com dados que ela nunca viu antes

$ann->loadDataSetCSV( dirname(__FILE__) . DIRECTORY_SEPARATOR . "pima-indians-diabetes.csv", "," );

# gerando arquivo no formado do dataset para a lib fann
$ann->createFannDataSet( dirname(__FILE__) . DIRECTORY_SEPARATOR . "fann_dataset.data" );

$ann->trainNetwork( dirname(__FILE__) . DIRECTORY_SEPARATOR . "fann_trained.net" );

// testing network
$ann->testNetworkAccuracy();

$endTime = microtime(true);

$time = abs( $endTime - $startTime );

echo "Time to load, train and test network: {$time} seconds" . chr(13).chr(10);






$startTime = microtime(true);


// now, you can simulate with any other input to predict
// without train again
// like this
$inputData = [ 8, 1, 0.547, 88, 45, 5, 77, 38 ];

// new object
$ann = new FannTrain;

// loading trained network
$ann->loadTrainedNetwok( dirname(__FILE__) . DIRECTORY_SEPARATOR . "fann_trained.net" );


echo chr(13).chr(10);

// if returns 1 is positive, or zero is negative
$test = $ann->runTest( $inputData ) == 1 ? "Positive" : "Negative";
echo "Test input result: {$test}" . chr(13).chr(10);


$endTime = microtime(true);
$time = abs( $endTime - $startTime );

echo chr(13).chr(10);

echo "Time only to run a input with an already trained network: {$time} seconds" . chr(13).chr(10);

// our network was created to receive an array with eight params
// and returns only one output
// you can modify anything to adapt to your problem

?>