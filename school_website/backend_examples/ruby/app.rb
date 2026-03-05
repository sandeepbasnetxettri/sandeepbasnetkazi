require 'sinatra'
require 'mysql2'
require 'bcrypt'
require 'json'

# Database Configuration
client = Mysql2::Client.new(
  host: "localhost",
  username: "root",
  password: "",
  database: "school_db"
)

post '/login' do
  content_type :json
  data = JSON.parse(request.body.read)
  
  username = data['username']
  password = data['password']
  role = data['role']

  query = client.prepare("SELECT id, username, password, role FROM users WHERE username = ? AND role = ?")
  results = query.execute(username, role).to_a

  if results.empty?
    status 401
    return { message: "Invalid credentials" }.to_json
  end

  user = results.first
  
  # Verify password
  begin
    is_match = BCrypt::Password.new(user['password']) == password
  rescue
    is_match = false
  end

  # Fallback for plain text
  if !is_match && password != user['password']
    status 401
    return { message: "Invalid credentials" }.to_json
  end

  {
    message: "Login successful",
    user: {
      id: user['id'],
      username: user['username'],
      role: user['role']
    }
  }.to_json
end
