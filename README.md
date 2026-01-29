# Quadro de Vagas Kombo

![WordPress](https://img.shields.io/badge/WordPress-6.0+-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4+-purple.svg)
![Elementor](https://img.shields.io/badge/Elementor-3.0+-pink.svg)
![License](https://img.shields.io/badge/license-GPL--2.0+-green.svg)

Plugin WordPress com widget Elementor para integrar vagas de emprego da plataforma [Kombo.com.br](https://www.kombo.com.br) diretamente no seu site.

---

## 📋 Índice

- [Recursos](#-recursos)
- [Requisitos](#-requisitos)
- [Instalação](#-instalação)
- [Configuração](#-configuração)
- [Uso](#-uso)
- [Layouts Disponíveis](#-layouts-disponíveis)
- [Filtros e Personalização](#-filtros-e-personalização)
- [Estrutura do Projeto](#-estrutura-do-projeto)
- [Desenvolvimento](#-desenvolvimento)
- [Contribuição](#-contribuição)
- [Suporte](#-suporte)
- [Licença](#-licença)

---

## ✨ Recursos

### Widget Elementor Completo
- 🎨 **3 Layouts**: Grid (Cards), Lista e Accordion
- 📱 **Totalmente Responsivo**: Adaptação automática para desktop, tablet e mobile
- 🎯 **Personalização Visual Completa**: Controles de cores, tipografia, espaçamento e estilos
- 🚀 **Sistema de Cache Inteligente**: Melhora a performance com cache configurável
- ♿ **Acessibilidade**: Suporte a navegação por teclado e leitores de tela
- 🔍 **Filtros Avançados**: Filtre vagas por localização, área, número de vagas e data

### Integração com Kombo
- ✅ Consumo automático do feed XML/RSS do Kombo
- ✅ Parsing inteligente de dados com tratamento de encoding UTF-8
- ✅ Extração automática de informações (cidade, estado, ramo, data, etc.)
- ✅ Limpeza de HTML e formatação de dados

### Otimizações
- ⚡ Cache com WordPress Transients API
- 🔄 Duração de cache configurável (1-1440 minutos)
- 🎭 Suporte a múltiplos CIDs (códigos de cliente)
- 📊 Tratamento completo de erros

---

## 📦 Requisitos

- **WordPress**: 6.0 ou superior
- **Elementor**: 3.0 ou superior
- **PHP**: 7.4 ou superior
- **Conta ativa** no [Kombo.com.br](https://www.kombo.com.br)

---

## 🚀 Instalação

### Método 1: Upload via WordPress

1. Baixe o arquivo `quadro-vagas-kombo.zip` da [última release](https://github.com/Agenciatektus/vagas-kombo-wordpress/releases)
2. No WordPress, vá em **Plugins** → **Adicionar novo** → **Enviar plugin**
3. Selecione o arquivo ZIP e clique em **Instalar agora**
4. Clique em **Ativar plugin**

### Método 2: Upload Manual via FTP

1. Extraia o arquivo `quadro-vagas-kombo.zip`
2. Envie a pasta `quadro-vagas-kombo` para `/wp-content/plugins/`
3. Ative o plugin em **Plugins** no WordPress

### Método 3: Git Clone

```bash
cd wp-content/plugins/
git clone https://github.com/Agenciatektus/vagas-kombo-wordpress.git quadro-vagas-kombo
```

Depois ative o plugin no WordPress.

---

## ⚙️ Configuração

### Obtendo seu CID (Código do Cliente)

1. Acesse sua conta no [Kombo.com.br](https://www.kombo.com.br)
2. Para **Kombo Estratégico**: vá em **Empresa** → **Dados Cadastrais**
3. Para **Kombo Grátis/Seleção**: vá em **Ferramentas** → **Ferramentas Prontas**
4. Copie seu **Código do Cliente (CID)**

---

## 🎨 Uso

### Adicionando o Widget no Elementor

1. Edite uma página com o **Elementor**
2. No painel lateral, procure por **"Kombo"** ou **"Vagas"**
3. Arraste o widget **"Quadro de Vagas Kombo"** para a página
4. Configure as opções no painel

### Aba Conteúdo

| Opção | Descrição | Padrão |
|-------|-----------|--------|
| **CID Kombo** | Código do cliente Kombo | (vazio) |
| **Layout** | Grid, Lista ou Accordion | Grid |
| **Colunas** | Número de colunas (apenas Grid) | 3 |
| **Limite de Vagas** | Quantidade máxima (0 = todas) | 9 |

### Opções de Exibição

| Opção | Descrição | Padrão |
|-------|-----------|--------|
| **Exibir Ramo de Atividade** | Mostrar área/ramo | Não |
| **Exibir Cidade** | Mostrar localização | Sim |
| **Exibir Número de Vagas** | Mostrar quantidade | Sim |
| **Exibir Data de Abertura** | Mostrar data da vaga | Não |

### Botão

| Opção | Descrição | Padrão |
|-------|-----------|--------|
| **Texto do Botão** | Texto customizado | "Candidatar-se" |
| **URL de Destino** | URL customizada (opcional) | URL Kombo |

---

## 🎭 Layouts Disponíveis

### 1. Grid (Cards)
Grid responsivo com cards de vagas:
- **Desktop**: 3 colunas (configurável)
- **Tablet**: 2 colunas
- **Mobile**: 1 coluna

### 2. Lista
Listagem horizontal com informações compactas e botão lateral.

### 3. Accordion
Cards expansíveis com navegação por teclado (Arrow keys, Home, End).

---

## 🔍 Filtros e Personalização

### Filtros Disponíveis

#### Filtrar por Localização
Filtre vagas por cidade ou estado:
```
Salvador
São Paulo/SP
Rio de Janeiro
```

#### Filtrar por Ramo/Área
Filtre por categoria de vaga:
```
Recursos Humanos
Tecnologia da Informação
Vendas
```

#### Número Mínimo de Vagas
Exiba apenas vagas com X ou mais posições disponíveis:
```
2 (mostra apenas vagas com 2+ posições)
```

#### Vagas dos Últimos X Dias
Filtre por data de abertura:
```
30 (últimos 30 dias)
60 (últimos 60 dias)
```

### Personalização de Estilo

#### Card
- Cor de fundo
- Espessura da borda (4 lados independentes)
- Cor da borda
- Raio da borda
- Padding
- Box shadow
- Efeito hover

#### Título da Vaga
- Cor
- Tipografia (fonte, tamanho, peso, etc.)
- Margem

#### Informações Secundárias
- Cor do texto
- Cor dos ícones
- Tipografia

#### Botão
- Cor de fundo
- Cor do texto
- Cor hover
- Tipografia
- Raio da borda
- Padding
- Largura total (opcional)

#### Espaçamento
- Gap entre cards (0-50px)

---

## 📁 Estrutura do Projeto

```
quadro-vagas-kombo/
├── quadro-vagas-kombo.php          # Arquivo principal do plugin
├── readme.txt                       # README WordPress oficial
├── .gitignore                       # Git ignore
├── includes/
│   ├── class-kombo-api.php         # Handler da API Kombo
│   ├── class-kombo-cache.php       # Sistema de cache
│   └── elementor-widgets/
│       └── class-kombo-vagas-widget.php  # Widget Elementor
└── assets/
    ├── css/
    │   └── vagas-widget.css        # Estilos frontend
    └── js/
        └── vagas-widget.js         # JavaScript (accordion)
```

### Arquivos Principais

#### `quadro-vagas-kombo.php`
- Inicialização do plugin
- Verificação de compatibilidade
- Registro de widgets e assets

#### `class-kombo-api.php`
- Consumo do feed XML do Kombo
- Parsing de dados com SimpleXML
- Tratamento de encoding UTF-8
- Limpeza de HTML

#### `class-kombo-cache.php`
- Sistema de cache com Transients
- Gerenciamento de cache por CID
- Métodos de limpeza de cache

#### `class-kombo-vagas-widget.php`
- Widget Elementor completo
- Controles de conteúdo e estilo
- Sistema de filtros
- Renderização de layouts

---

## 🛠️ Desenvolvimento

### Requisitos de Desenvolvimento

```bash
git clone https://github.com/Agenciatektus/vagas-kombo-wordpress.git
cd vagas-kombo-wordpress
```

### Estrutura de Commits

O projeto segue a convenção [Conventional Commits](https://www.conventionalcommits.org/):

```
feat: nova funcionalidade
fix: correção de bug
docs: documentação
style: formatação de código
refactor: refatoração
test: testes
chore: manutenção
```

### Código de Qualidade

- ✅ Sanitização de inputs com `sanitize_text_field()`, `absint()`, etc.
- ✅ Escape de outputs com `esc_html()`, `esc_attr()`, `esc_url()`
- ✅ Prepared statements para queries
- ✅ Nonces para formulários
- ✅ Verificação de capabilities

### Hooks Disponíveis

O plugin não fornece hooks customizados no momento, mas você pode usar os hooks padrão do WordPress/Elementor.

---

## 🤝 Contribuição

Contribuições são bem-vindas! Por favor:

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/MinhaFeature`)
3. Commit suas mudanças (`git commit -m 'feat: adiciona nova feature'`)
4. Push para a branch (`git push origin feature/MinhaFeature`)
5. Abra um Pull Request

### Diretrizes

- Siga os padrões de código WordPress
- Teste em múltiplas versões do WordPress/Elementor
- Documente novas funcionalidades
- Mantenha a compatibilidade com PHP 7.4+

---

## 📞 Suporte

### Plugin

Para questões relacionadas ao plugin:
- 🐛 [Abra uma issue](https://github.com/Agenciatektus/vagas-kombo-wordpress/issues)
- 💬 [Discussões](https://github.com/Agenciatektus/vagas-kombo-wordpress/discussions)

### Plataforma Kombo

Para suporte da plataforma Kombo.com.br:
- 📧 Email: suportecliente@kombo.com.br
- ☎️ Telefone: (48) 3374-4373
- 🌐 Site: [www.kombo.com.br](https://www.kombo.com.br)

---

## 📄 Licença

Este projeto está licenciado sob a licença GPL v2 ou superior - veja o arquivo [LICENSE](LICENSE) para detalhes.

```
Copyright (C) 2024 Agência Tektus

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

---

## 👏 Créditos

### Desenvolvido por
**Agência Tektus**
- 🌐 Website: [tektus.com.br](https://tektus.com.br)
- 📧 Email: contato@tektus.com.br

### Integração
Plataforma **Kombo** - Sistema de recrutamento e seleção
- 🌐 [www.kombo.com.br](https://www.kombo.com.br)

### Tecnologias
- WordPress
- Elementor
- PHP
- SimpleXML
- JavaScript (ES6+)

---

## 🔄 Changelog

### [1.0.0] - 2024-01-29

#### Adicionado
- Widget Elementor com 3 layouts (Grid, Lista, Accordion)
- Sistema de cache com WordPress Transients
- Consumo do feed XML do Kombo
- Tratamento de encoding UTF-8
- Limpeza de HTML nas descrições
- Controles completos de estilo
- Sistema de filtros avançado (localização, área, vagas, data)
- Controles de borda (espessura e cor)
- Responsividade completa
- Acessibilidade (ARIA, navegação por teclado)
- Documentação completa

---

<p align="center">
  Feito com ❤️ por <a href="https://tektus.com.br">Agência Tektus</a>
</p>
