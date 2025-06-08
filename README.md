# Plano de Implementação – Trabalho Prático 01/2025  
**Disciplina:** DS122 - Desenvolvimento de Aplicações Web 1  
**Professor:** Alexander Robert Kutzke  
**Curso:** Tecnologia em Análise e Desenvolvimento de Sistemas  
**Instituição:** Universidade Federal do Paraná - UFPR  
**Setor:** Setor de Educação Profissional e Tecnológica (SEPT)  

## 1. Visão Geral

Este documento descreve o plano para o desenvolvimento de uma aplicação web completa cujo objetivo é implementar um **jogo de digitação** com funcionalidades de autenticação, pontuação, criação e participação em ligas, histórico de partidas e rankings.

A aplicação será desenvolvida utilizando HTML5, CSS3, JavaScript (com suporte de jQuery), PHP e um banco de dados (como MySQL).

---

## 2. Funcionalidades Principais

### 2.1 Autenticação de Usuário
- Registro de novo usuário com validações.
- Login/autenticação de usuários.
- Sessões para manter o usuário autenticado.

### 2.2 Jogo de Digitação (100% em JavaScript)
- Palavras exibidas para digitação.
- Cronômetro e pontuação baseada em acertos.
- Feedback visual sobre erros e acertos.

### 2.3 Pontuação
- Armazenamento das pontuações no banco de dados.
- Exibição da pontuação por partida.
- Cálculo e exibição de pontuação:
  - Geral (desde o início do sistema e semanal).
  - Por liga (desde a criação da liga e semanal).

### 2.4 Ligas
- Criação de ligas com palavra-chave.
- Participação em ligas existentes com palavra-chave.
- Ranking de usuários por liga.

### 2.5 Histórico e Relatórios
- Lista completa das partidas jogadas pelo usuário.
- Relatório com data, pontuação e tempo de cada partida.

---

## 3. Tecnologias Utilizadas

| Camada         | Tecnologias                                |
|----------------|--------------------------------------------|
| Front-end      | HTML5, CSS3, JavaScript, jQuery, Bootstrap |
| Back-end       | PHP (sem frameworks)                       |
| Banco de Dados | MySQL ou MariaDB                           |

---

## 4. Estrutura do Projeto

/projeto
│
├── index.php # Página inicial
├── login.php # Login de usuário
├── register.php # Registro de usuário
├── dashboard.php # Tela principal com acesso ao jogo e relatórios
├── game.js # Lógica do jogo de digitação (JS puro)
├── game.php # Tela para jogar
├── score.php # Armazena e exibe pontuações
├── leagues.php # Criação e participação em ligas
├── rankings.php # Rankings geral e por liga
├── report.php # Relatórios de partidas
│
├── /css
│ └── style.css
├── /js
│ └── validaForm.js
├── /includes
│ ├── db.php # Conexão com banco de dados
│ ├── auth.php # Autenticação de usuários
│ └── utils.php # Funções auxiliares
│
└── README.md

---

## 5. Banco de Dados (Modelo Simplificado)

### Tabelas

- `usuarios(id, nome, email, senha, criado_em)`
- `partidas(id, usuario_id, pontos, data_partida)`
- `ligas(id, nome, palavra_chave, criado_por, criado_em)`
- `ligas_usuarios(id, liga_id, usuario_id)`
- `pontuacoes(id, usuario_id, liga_id, pontos, data_partida)`

---

## 6. Cronograma de Desenvolvimento

| Semana | Atividade                                                       |
|--------|-----------------------------------------------------------------|
| 1      | Planejamento, criação do repositório, setup inicial do ambiente |
| 2      | Desenvolvimento do sistema de autenticação e registro           |
| 3      | Implementação da lógica do jogo em JavaScript                   |
| 4      | Armazenamento e exibição da pontuação                           |
| 5      | Funcionalidade de ligas (criação e entrada)                     |
| 6      | Rankings geral e por liga, semanal e total                      |
| 7      | Relatórios de partidas e testes finais                          |
| 8      | Documentação, README e defesa                                   |

---

## 7. Metodologia

- Divisão de tarefas entre os integrantes do grupo.
- Controle de versão com Git e repositório no GitHub/GitLab.
- Uso de issues e milestones para gerenciar o progresso.

---

## 8. Requisitos Atendidos

### Front-end
- [x] HTML5, CSS3, JS
- [x] Interface amigável com validações
- [x] Jogo 100% em JavaScript

### Back-end
- [x] PHP sem frameworks
- [x] Integração com banco de dados
- [x] Validações no servidor

---

## 9. Entrega

- Repositório com código completo, acessível via link no Moodle.
- Apresentação para o professor com demonstração funcional e explicação do código.
- README.md completo com instruções de uso.

---

## 10. Interface e funcionalidades



---

## 11. Grupo

- Nome 1 – RA – email
- Nome 2 – RA – email
- Nome 3 – RA – email

---