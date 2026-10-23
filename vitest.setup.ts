import 'dotenv/config'

// Testes de integração usam o banco de TESTE, nunca o banco de revisão.
if (process.env.DATABASE_URL_TEST) process.env.DATABASE_URL = process.env.DATABASE_URL_TEST
