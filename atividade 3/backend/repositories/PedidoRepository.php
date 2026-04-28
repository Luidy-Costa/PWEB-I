<?php

namespace Repositories;

use Models\Pedido;

class PedidoRepository {
    private string $arquivoJson;

    public function __construct() {
        // Define o caminho do arquivo JSON que servirá como nosso "banco de dados"
        $this->arquivoJson = __DIR__ . '/../data/pedidos.json';
        $this->inicializarArquivo();
    }

    // Garante que o arquivo e a pasta existam antes de tentarmos salvar algo
    private function inicializarArquivo(): void {
        $diretorio = dirname($this->arquivoJson);
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0777, true);
        }
        
        if (!file_exists($this->arquivoJson)) {
            file_put_contents($this->arquivoJson, json_encode([]));
        }
    }

    public function salvar(Pedido $pedido): void {
        $pedidos = $this->listarTodos();
        
        // Estrutura o objeto Pedido para ser salvo no JSON
        $pedidoArray = [
            'status' => $pedido->getStatus(),
            'valorTotal' => $pedido->getValorTotal(),
            'itens' => array_map(function($item) {
                return [
                    'produto_id' => $item->getProdutoId(),
                    'quantidade' => $item->getQuantidade(),
                    'precoUnitario' => $item->getPrecoUnitario(),
                    'subtotal' => $item->getSubtotal()
                ];
            }, $pedido->getItens())
        ];

        // Adiciona o novo pedido ao array existente e salva no arquivo
        $pedidos[] = $pedidoArray;
        file_put_contents($this->arquivoJson, json_encode($pedidos, JSON_PRETTY_PRINT));
    }

    public function listarTodos(): array {
        $conteudo = file_get_contents($this->arquivoJson);
        return json_decode($conteudo, true) ?? [];
    }
}