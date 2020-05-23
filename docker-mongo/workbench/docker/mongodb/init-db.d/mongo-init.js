db.auth('admin', 'admin')

db = db.getSiblingDB('local')

db.createUser({
  user: 'test_user',
  pwd: 'test_password',
  roles: [
    {
      role: 'root',
      db: 'admin',
    },
  ],
});