# 📋 Guia Completo: Submissão ao WordPress.org

Guia passo a passo para submeter o plugin "Quadro de Vagas Kombo" ao repositório oficial do WordPress.

---

## 📝 Pré-requisitos (✅ Já Concluído)

- [x] Plugin funcional e testado
- [x] Licença GPL v2 or later
- [x] readme.txt no formato WordPress.org
- [x] Código sanitizado e com escape
- [x] Sem links externos não autorizados
- [x] Sem conteúdo ofensivo ou ilegal

---

## 🚀 Passo 1: Criar Conta no WordPress.org

1. Acesse: https://login.wordpress.org/register
2. Preencha os dados:
   - **Username**: `agenciatektus` (ou outro disponível)
   - **Email**: Use email da agência
3. Confirme email
4. Anote o username - você precisará atualizar no readme.txt

**Após criar a conta, atualize o readme.txt:**
```
Contributors: agenciatektus
```

---

## 🔍 Passo 2: Validar o readme.txt

1. Acesse: https://wordpress.org/plugins/developers/readme-validator/
2. Copie o conteúdo completo do arquivo `readme.txt`
3. Cole no validador
4. Corrija quaisquer erros apontados

**Ou use linha de comando:**
```bash
curl -F "readme=@quadro-vagas-kombo/readme.txt" https://wordpress.org/plugins/developers/readme-validator/
```

---

## 📸 Passo 3: Preparar Screenshots (IMPORTANTE)

O WordPress.org requer screenshots para aprovação. Crie imagens demonstrando:

**Screenshots necessários:**
1. `screenshot-1.png` - Widget no editor Elementor (1280x720px ou maior)
2. `screenshot-2.png` - Layout Grid exibindo vagas
3. `screenshot-3.png` - Layout Lista
4. `screenshot-4.png` - Layout Accordion
5. `screenshot-5.png` - Painel de configurações de estilo

**Onde colocar:**
```
quadro-vagas-kombo/
├── assets/
│   ├── screenshot-1.png
│   ├── screenshot-2.png
│   ├── screenshot-3.png
│   ├── screenshot-4.png
│   └── screenshot-5.png
```

**Especificações:**
- Formato: PNG ou JPG
- Resolução mínima: 1280x720px
- Tamanho máximo: 1MB por imagem
- Nomes: `screenshot-1.png`, `screenshot-2.png`, etc.

---

## 📤 Passo 4: Submeter o Plugin

1. Faça login em: https://wordpress.org/plugins/developers/add/

2. Preencha o formulário:

   **Plugin Name:**
   ```
   Quadro de Vagas Kombo
   ```

   **Plugin Description:**
   ```
   Integre vagas de emprego do Kombo.com.br no seu site WordPress com widget Elementor personalizável.

   Recursos principais:
   - Widget Elementor com 3 layouts (Grid, Lista, Accordion)
   - Filtros avançados (localização, área, vagas, data)
   - Personalização visual completa
   - Sistema de cache inteligente
   - 100% responsivo
   - Atualizações automáticas via GitHub

   Requer conta ativa no Kombo.com.br para obter o CID (Código do Cliente).
   ```

   **Plugin URL:**
   ```
   https://github.com/Agenciatektus/vagas-kombo-wordpress
   ```

3. Faça upload do arquivo ZIP:
   - Use o arquivo `quadro-vagas-kombo.zip`
   - Máximo 10MB (nosso tem 23KB ✓)

4. Confirme que:
   - [ ] Você leu e concorda com as diretrizes
   - [ ] O plugin é compatível com GPL v2+
   - [ ] Você tem direitos para distribuir o plugin
   - [ ] O plugin não contém código malicioso

5. Clique em **Submit Plugin for Review**

---

## ⏱️ Passo 5: Aguardar Revisão Manual

**Tempo médio:** 7-14 dias (pode variar)

Durante a revisão, a equipe do WordPress.org verificará:
- Segurança do código
- Conformidade com diretrizes
- Licenciamento
- Qualidade geral

**Você receberá:**
- ✅ **Aprovação** → Acesso ao repositório SVN
- ❌ **Pendências** → Email com correções necessárias

---

## 📦 Passo 6: Configurar SVN (Após Aprovação)

Quando aprovado, você receberá:
- URL do repositório SVN: `https://plugins.svn.wordpress.org/quadro-vagas-kombo/`
- Acesso de commit com seu username

**Instalar SVN:**

**Windows:**
```bash
# Usando Chocolatey
choco install tortoisesvn

# Ou baixe manualmente
https://tortoisesvn.net/downloads.html
```

**macOS:**
```bash
brew install svn
```

**Linux:**
```bash
sudo apt-get install subversion
```

---

## 🚀 Passo 7: Fazer Upload Inicial do Plugin

### 7.1 Checkout do Repositório

```bash
cd c:\Users\Peterson\Documents\Tektus\Plugin-vagas-kombo
svn checkout https://plugins.svn.wordpress.org/quadro-vagas-kombo svn-quadro-vagas-kombo
cd svn-quadro-vagas-kombo
```

### 7.2 Estrutura do SVN

O SVN terá esta estrutura:
```
svn-quadro-vagas-kombo/
├── trunk/          # Versão de desenvolvimento
├── tags/           # Releases estáveis
└── assets/         # Screenshots e banners
```

### 7.3 Copiar Arquivos para Trunk

```bash
# Copiar todos os arquivos do plugin para trunk/
cp -r ../vagas-kombo-wordpress/quadro-vagas-kombo/* trunk/
```

### 7.4 Adicionar Screenshots em Assets

```bash
# Criar pasta assets se não existir
mkdir -p assets

# Copiar screenshots
cp ../vagas-kombo-wordpress/quadro-vagas-kombo/assets/screenshot-*.png assets/

# Opcional: Adicionar banner e ícone
# banner-1544x500.png  (banner principal)
# banner-772x250.png   (banner menor)
# icon-128x128.png     (ícone pequeno)
# icon-256x256.png     (ícone grande)
```

### 7.5 Adicionar Arquivos ao SVN

```bash
# Adicionar todos os arquivos
svn add trunk/* --force
svn add assets/* --force

# Verificar status
svn status
```

### 7.6 Commit para SVN

```bash
svn commit -m "Versão inicial 1.0.0 - Lancamento do plugin Quadro de Vagas Kombo"
```

**Credenciais:**
- Username: `agenciatektus` (seu username WordPress.org)
- Password: (sua senha WordPress.org)

### 7.7 Criar Tag da Versão 1.0.0

```bash
# Copiar trunk para tags/1.0.0
svn copy trunk tags/1.0.0

# Commit da tag
svn commit -m "Tagging version 1.0.0"
```

---

## ✅ Passo 8: Verificar Publicação

**Aguarde 15-30 minutos** após o commit.

Seu plugin estará disponível em:
```
https://wordpress.org/plugins/quadro-vagas-kombo/
```

**Verificações:**
- [ ] Página do plugin está acessível
- [ ] Screenshots estão aparecendo
- [ ] Botão "Download" funciona
- [ ] Informações do readme.txt estão corretas
- [ ] Plugin aparece na busca do WordPress.org

---

## 🔄 Futuras Atualizações

Para lançar uma nova versão (ex: 1.0.1):

### 1. Atualizar Código
```php
// Em quadro-vagas-kombo.php
Version: 1.0.1
define( 'KOMBO_VAGAS_VERSION', '1.0.1' );
```

### 2. Atualizar readme.txt
```
Stable tag: 1.0.1

== Changelog ==
= 1.0.1 - 2024-02-15 =
* fix: Correção de bug X
* improvement: Melhoria Y
```

### 3. Atualizar SVN
```bash
cd svn-quadro-vagas-kombo

# Atualizar trunk com novos arquivos
cp -r ../vagas-kombo-wordpress/quadro-vagas-kombo/* trunk/

# Commit trunk
svn commit -m "Update trunk to version 1.0.1"

# Criar tag da nova versão
svn copy trunk tags/1.0.1
svn commit -m "Tagging version 1.0.1"
```

### 4. Criar Release no GitHub
```bash
git tag v1.0.1
git push origin v1.0.1
```

Crie release no GitHub anexando o ZIP atualizado.

---

## 📊 Recursos Úteis

- **Plugin Guidelines:** https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/
- **Readme Standard:** https://developer.wordpress.org/plugins/wordpress-org/how-your-readme-txt-works/
- **SVN Guide:** https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/
- **Assets Guidelines:** https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/

---

## 🆘 Problemas Comuns

### Plugin não aparece após commit
- Aguarde 30 minutos
- Verifique se a tag foi criada corretamente
- Confirme que `Stable tag:` no readme.txt está correto

### Screenshots não aparecem
- Verifique se estão na pasta `assets/` (não `trunk/assets/`)
- Confirme os nomes: `screenshot-1.png`, `screenshot-2.png`, etc.
- Tamanho máximo: 1MB por imagem

### Erro de autenticação SVN
- Use username WordPress.org (não email)
- Confirme que você tem permissões de commit
- Tente salvar credenciais: `svn commit --username agenciatektus`

---

## ✨ Próximos Passos Após Aprovação

1. ✅ Promover o plugin:
   - Adicionar ao site da agência
   - Anunciar em redes sociais
   - Criar post no blog

2. 📈 Monitorar:
   - Downloads em https://wordpress.org/plugins/quadro-vagas-kombo/advanced/
   - Reviews e ratings
   - Support forum

3. 🔧 Manutenção:
   - Responder tickets de suporte
   - Corrigir bugs reportados
   - Adicionar novos recursos

---

**Desenvolvido por Agência Tektus**
- 🌐 https://agenciatektus.com.br
- 📧 contato@agenciatektus.com.br
