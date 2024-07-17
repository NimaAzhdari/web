using System.Data;
using Microsoft.Data.SqlClient;
using Dapper;
using MvcDapper.Models;
using Microsoft.IdentityModel.Tokens;

namespace MvcDapper.DataAccess;
public class UserRepository
{
    private readonly string _connectionString;

    public UserRepository(string connectionString)
    {
        _connectionString = connectionString;
    }
      private IDbConnection Connection => new SqlConnection(_connectionString);

    public int AddUser(SignupViewModel user)//1==username was uniqe and insert happend & 0==user name not uniqe
    {
        using (IDbConnection dbConnection = Connection)
        {
            try
        {
            dbConnection.Open();

            string fQuery = "SELECT * FROM UserData WHERE Username = @Username";
            var existingUser = dbConnection.Query<SignupViewModel>(fQuery, new { Username = user.Username }).FirstOrDefault();

            if (existingUser == null)// Username is unique, insert the user
            {
                string sQuery = "INSERT INTO UserData (Username, Password, Phone) VALUES(@Username, @Password, @Phone)";
                dbConnection.Execute(sQuery, user);
                return 1; // Insert successful
            }
            else
            {
                return 0; // Username not unique
            }
        }
        catch (Exception ex)
        {
            throw new Exception("An error occurred while adding the user.", ex);
        }
        finally
        {
            dbConnection.Close();
        }
        }
    }

    public SignupViewModel GetUser(LoginViewModel user)
    {
         using (IDbConnection dbConnection = Connection)
         {
            try
            {
                dbConnection.Open();
                string sQuery ="SELECT * FROM UserData WHERE Username = @Username AND Password = @Password";
                return dbConnection.Query<SignupViewModel>(sQuery, new { Username = user.Username, Password = user.Password }).FirstOrDefault();
            }
            catch(Exception ex)
            {
                throw new Exception("An error occurred while adding the user.", ex);
            }
            finally
            {
                dbConnection.Close();
            }
            
         }
    }
}