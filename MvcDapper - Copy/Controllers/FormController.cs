using Microsoft.AspNetCore.Mvc;
using MvcDapper.DataAccess;
using MvcDapper.Models;

namespace MvcDapper.Controllers;
public class FormController:Controller
{
    private readonly UserRepository _userRepository;

    public FormController(UserRepository userRepository)
    {
        _userRepository = userRepository;
    }
     [HttpPost]
    public IActionResult Signup(SignupViewModel user)
    {
        if (ModelState.IsValid)
        {
            var result=_userRepository.AddUser(user);
            if(result == 1)
            {
                return RedirectToAction("Login","Home");
            }
            else 
            {  
                 TempData["message"]="نام کاربری تکراری است";
                return RedirectToAction("Signup","Home");
            }
            
        }
        else
        {
           TempData["message"]="دوباره تلاش کنید";
            return RedirectToAction("Signup","Home");
        }
    }
     [HttpPost]
        public IActionResult Login(LoginViewModel userinput)
        {
            var user = _userRepository.GetUser(userinput);
            if (user != null)
            {
               TempData["message"] = "خوش آمدی" + user.Username;
                return RedirectToAction("Login", "Home");
            }
            else
            {
                TempData["message"]="اطلاعات وارد شده غلط می باشد";
                return RedirectToAction("Login", "Home");
            }
        }
}