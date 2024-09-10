namespace MvcRazor.Data;
using MvcRazor.Models;
using Microsoft.EntityFrameworkCore;
public class ShopContext :DbContext
{
   public DbSet<Product> Products { get; set; }
   public DbSet<category_product> CategoryProduct { get; set; }
   public DbSet<montakhab_product> MontakhabProduct { get; set; }
   public DbSet<vitrin_product> VitrinProduct { get; set; }
    protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
        {
            optionsBuilder.UseSqlite(@"Data source=neshat.db");
        }
}
