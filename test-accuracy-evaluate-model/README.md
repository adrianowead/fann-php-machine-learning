### Teste de precisão

Este exemplo demonstra como é possível acompanhar a evolução da rede neural a cada nova geração (**epoch**).

Seguindo as boas práticas de modelagem de redes neurais, o teste de precisão deve ser feito com dados que a rede nunca _"viu"_. Desta forma podemos ter mais confiança nos resultados de precisão, pois uma coisa é testar a precisão com um conjunto de dados que ela recebeu para treinamento, e outra coisa é utilizar dados que ela não usou para treinar.

### Problema proposto

Os dados (**dataset**) utilizados para este experimento, foram forncecidos pelo [National Institute of Diabetes and Digestive and Kidney Diseases](https://www.niddk.nih.gov/) e obtidos no site [Kaggle](https://www.kaggle.com/uciml/pima-indians-diabetes-database).

O objetivo é determinar se o paciente possui ou não diabetes, utilizando como base os dados de diagnósticos.

#### Estrutura dos dados

Os dados são bastante simples, consistem em um arquivo CSV, onde os oito primeiros campos são os resultados dos exames, e o último campo é o resultado, com valores `0` ou `1`.

Veja a primeira linha:

```
6,148,72,35,0,33.6,0.627,50,1
```

#### Avaliando desempenho

Para executar este exemplo basta você executar o PHP no seu terminal da seguinte forma:

```
$ php start.php
```

Durante o processo ele irá gerar um arquivo **.data**, contendo o mesmo conteúdo do arquivo, mas com a estrutura necessária para iniciar o treinamento da rede neural.

Veja o exemplo do formato do arquivo para o treinamento da função [**xor**](https://raw.githubusercontent.com/bukka/php-fann/master/examples/logic_gates/xor.data).

Neste ponto, uma parte dos registros não serão inseridos neste arquivo para que a rede não treine com eles, serão usados durante o treinamento para avaliar o nível de precisão da rede a cada _epoch_.

Ao final do treinamento será gerado um arquivo **.net** contendo a estrutura final e todos os pesos treinados pela rede. Este arquivo é o estado da rede exatamente no final do último _epoch_, além de informações sobre as camadas de neuônios utilizadas, quantos inputs ela espera e quantos outputs ela retornará.

Com este arquivo **.net** gerado, você pode usar sua rede neural em qualquer lugar que rode a FANN. A utilização da rede é extremamente rápida, a parte lenta é o treinamento da rede.

É importante observar que, se você treinar a rede para ter _n_ inputs e _y_ outputs. Não poderá modificar isso depois, qualquer mudança na estrutura da rede, demendará um novo treinamento do zero.

No nosso exemplo, a rede é flexível para receber qualquer quantidade de inputs, mas sempre terá **apenas um output**.

O script está montado de uma forma que o último campo do csv sempre é entendido como o resultado do processamento das outras colunas. Mas reforçando, uma vez treinado isso é fixo.

O mesmo vale para as camadas ocultas de neurônios, uma vez defindas quantas camadas terá e as quantidades de neurônios, não pode ser modificado.

#### Durante o treinamento

No arquivo start.php, você pode modificar os parâmetros para testar se o desempenho será melhor ou pior com esta ou aquela configuração.

Por padrão, durante o treinamento o sistema avalia a performance da rede a cada 100 epochs, é possível que de uma avaliação para outra a precisão da rede diminua. Isso acontece por que a cada geração são ajustados os pesos da rede, tentando abstrair cada vez mais os dados recebidos.

Esta abstração faz com que a rede consiga generalizar e prever resultados com os dados que ela ainda não conhece, que é o grande objetivo das redes neurais, conseguir prever (**predict**) um resultado com dados desconhecidos.

Geralmente quando o desempenho começa a cair muito a cada geração, os testes são interrompidos. Pois a chance da rede não estar aprendendo nada é muito grande, e certamente não servirá no mundo real.

#### A necessidade de testes

O objetivo de testar a rede com dados _"reais"_, é garantir que ela não faça um [overfitting](https://en.wikipedia.org/wiki/Overfitting), que é quando a rede foi mal configurada ou os dados não estão bem estruturados. O overfitting ocorre quando a rede não é capaz de generalizar o problema e consegue apenas resolver os dados treinados, e não dados do mundo real.