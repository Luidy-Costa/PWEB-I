// Mantém o estado do carrinho isolado no frontend
let carrinho = [];

function adicionarAoCarrinho(produto_id, nome, preco, tipo) {
    // Verifica se o item já existe no carrinho para apenas aumentar a quantidade
    const itemExistente = carrinho.find(item => item.produto_id === produto_id);
    
    if (itemExistente) {
        itemExistente.quantidade += 1;
    } else {
        carrinho.push({
            produto_id: produto_id,
            nome: nome,
            preco: preco,
            tipo: tipo,
            quantidade: 1
        });
    }
    atualizarInterfaceCarrinho();
}

function atualizarInterfaceCarrinho() {
    const lista = document.getElementById('lista-carrinho');
    lista.innerHTML = ''; // Limpa a lista antes de renderizar
    
    carrinho.forEach(item => {
        const li = document.createElement('li');
        li.textContent = `${item.quantidade}x ${item.nome} - R$ ${item.preco.toFixed(2)}`;
        lista.appendChild(li);
    });
}

// Comunicação via API (Integração Frontend/Backend)
async function finalizarPedido() {
    if (carrinho.length === 0) {
        alert("Adicione itens ao carrinho primeiro!");
        return;
    }

    const formaPagamento = document.getElementById('pagamento').value;
    const mensagemRetorno = document.getElementById('mensagem-retorno');
    mensagemRetorno.textContent = "Processando pedido...";

    // Monta o payload (pacote de dados) para enviar ao backend
    const payload = {
        itens: carrinho,
        pagamento: formaPagamento
    };

    try {
        // Envia o POST para a nossa API em PHP
        const resposta = await fetch('http://localhost:8000/pedidos', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const dados = await resposta.json();

        if (resposta.ok) {
            mensagemRetorno.textContent = `✅ ${dados.mensagem} Total a pagar: R$ ${dados.total_com_desconto.toFixed(2)}`;
            carrinho = []; // Limpa o carrinho
            atualizarInterfaceCarrinho();
        } else {
            mensagemRetorno.textContent = `❌ Erro: ${dados.erro}`;
            mensagemRetorno.style.color = "red";
        }
    } catch (erro) {
        console.error("Erro na requisição:", erro);
        mensagemRetorno.textContent = "❌ Falha ao conectar com o servidor.";
        mensagemRetorno.style.color = "red";
    }
}