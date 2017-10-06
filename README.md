## FANN - PHP Machine Learning
#### Utilizando uma Rede Neural Recorrente multicamadas Full-Connected - Backpropagation


Este repositório tem como objetivo reunir exemplos de como utilizar a biblioteca [FANN](http://leenissen.dk/fann/wp/), portada para o PHP como uma [extensão](http://leenissen.dk/fann/fann.html), com documentação disponível no [site oficial](http://php.net/manual/en/book.fann.php) da linguagem.

Para executar os exemplos, tenha instalado:

[PHP >= 7.0](https://secure.php.net/index.php#id2017-09-29-1)
[PHP FANN >= 2](https://github.com/bukka/php-fann)

Cada diretório possui seu próprio arquivo README.md com instruções específicas.

### Motivação

O objetivo é fornecer exemplos de como utilizar redes neurais com PHP e explorar um pouco mais a extensão FANN. Apesar de outras linguagens serem mais apropriadas para trabalhar com redes neurais, a ideia de usar esta tecnologia também com o PHP é muito interessante.

Talvez com um interesse maior da comunidade, futuramente o PHP esteja mais preparado para ligar com essa demanda. Pois seria ótimo poder fazer um amplo uso de redes neurais sem depender de outras tecnologias ou APIs externas.

Por enquanto a biblioteca FANN não suporta diversos tipos de redes mais complexas como:

[Convolutional Neural Network](https://en.wikipedia.org/wiki/Convolutional_neural_network)
[Long Short-Term Memory](https://en.wikipedia.org/wiki/Long_short-term_memory)

Que são utilizadas para problemas mais complexos, como reconhecimento facial, processamento de linguagem natural, etc...

Estas são tarefas que até podem ser realizadas por redes mais simples, porém o custo de processamento e complexidade de implementar são muito maiores.

Redes que utilizam **LSTM**, são capazes de abstrair praticamente qualquer tipo de problema, claro que demandam mais poder de processamento e mais neurônios. Porém os resultados costumam ser mais eficientes.

Cara tipo de rede tem como objetivo resolver um problema, ou resolver o mesmo problema mas com mais eficiência que outras redes.

Tudo depende de como você modela (configura) a rede, dos dados que devem ser processados e de quanto poder computacional você dispõe.

Pois mesmo que você tenha a rede modelada para o problema, seu computador ou servidor conseguem processar esses dados?

Um problema que geralmente acontece, é seu computador demorar muito tempo para treinar uma única geração da rede (**Epoch**), isso pode ocorrer por que a rede está mal modelada, ou não é adequada para os dados inseridos, ou simplesmente o computador não conseguir processar em tempo hábil.

Nestes casos, você pode recorrer ao processamento em GPU.

### PHP

Enfim, mesmo que não seja possível _até agora_, fazer tudo isso com o PHP. Podemos utilizar redes mais simples, como [Redes Neurais Recorrentes](https://en.wikipedia.org/wiki/Recurrent_neural_network), e utilizar redes do tipo [Feedfoward](https://en.wikipedia.org/wiki/Feedforward_neural_network) e [Backpropagation](https://en.wikipedia.org/wiki/Backpropagation), atualmente suportadas pela biblioteca FANN.