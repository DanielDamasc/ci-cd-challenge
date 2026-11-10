# Desafio CI/CD — Seminário

[![Board Status](https://dev.azure.com/dandamasceno04/b979674b-0c9b-4b99-97bf-30e95b9c25d5/39ce4d65-2203-4214-8db4-4fe8d1a0c3b5/_apis/work/boardbadge/4a25ae1a-4454-43df-b79b-11727d7c4e8d)](https://dev.azure.com/dandamasceno04/b979674b-0c9b-4b99-97bf-30e95b9c25d5/_boards/board/t/39ce4d65-2203-4214-8db4-4fe8d1a0c3b5/Issues/)

Projeto de seminário universitário: uma pipeline de CI/CD completa que builda a aplicação, publica a imagem Docker num registro e faz o deploy automático em um servidor, atualizando apenas o container do cliente em questão — sem derrubar o banco de dados nem os demais clientes.

## Estrutura do pipeline

1. **Build** — o GitHub Actions compila a aplicação e builda a imagem Docker.
2. **Push** — a imagem é publicada no Docker Hub, com tag versionada (SemVer + short hash do commit, nunca `latest`).
3. **Deploy** — o GitHub se conecta ao servidor via SSH e atualiza apenas o container da aplicação do cliente (o banco de dados não é tocado).

Os workflows ficam em arquivos separados:
- [`.github/workflows/ci.yml`](.github/workflows/ci.yml) — testes automatizados, roda em todo push/PR para `main`.
- [`.github/workflows/cd.yml`](.github/workflows/cd.yml) — build, push e deploy; só executa depois que o CI terminar com sucesso na `main`.

## Arquitetura

A aplicação é um sistema Laravel simples, com uma demonstração de persistência em banco de dados na rota raiz. A mesma imagem Docker roda para todos os clientes — não é multi-tenancy (cada cliente tem seu próprio banco), é a mesma base de código com uma instância totalmente isolada por cliente:

- [`deploy/project/`](deploy/project/) — código-fonte da aplicação Laravel.
- [`deploy/proxy/`](deploy/proxy/) — Traefik (proxy reverso), container único compartilhado por todos os clientes, roteando por `Host()` via labels Docker.
- [`deploy/cliente1/`](deploy/cliente1/) e [`deploy/cliente2/`](deploy/cliente2/) — instâncias isoladas por cliente: container da app + MySQL próprio, cada um em sua rede Docker privada.
- [`deploy/pma/`](deploy/pma/) — phpMyAdmin único, apontado para os bancos de todos os clientes.

O `cliente2` vai além do mínimo exigido pela proposta — foi adicionado pelo grupo para demonstrar que a arquitetura escala para múltiplos clientes sem mudanças estruturais.

## Decisões de escopo

- **Sem TLS/Let's Encrypt.** A proposta pede apenas para "considerar o uso do Proxy Reverso", sem exigir HTTPS. O acesso é feito via `http://`, usando hostnames do [sslip.io](https://sslip.io) (sem precisar de domínio próprio).
- **Integração com Azure Boards.** Item opcional da proposta: o repositório GitHub foi conectado ao Azure DevOps (via *GitHub connections*) para vincular commits e Pull Requests às Issues do Azure Boards (sintaxe `AB#<número>`). Essa integração é independente do pipeline de CI/CD — o GitHub Actions continua responsável por todo o build/push/deploy; o Azure Boards só acompanha o rastreamento de tarefas.
- **Observabilidade.** Outro item opcional da proposta, ainda em avaliação pelo grupo (Dozzle ou Prometheus+Grafana, roteado via Traefik).

## Rodando localmente (dentro de `deploy/project/`)

```sh
composer install
cp .env.example .env && php artisan key:generate
npm ci && npm run build
php artisan test
```

O banco de testes usa SQLite em memória — não precisa de MySQL rodando para executar `php artisan test`.
