-- Sprint ECO: plataforma de origem das mensagens de contato (?plataforma=slug).
ALTER TABLE submissions ADD COLUMN platform VARCHAR(80) NULL AFTER subject;
