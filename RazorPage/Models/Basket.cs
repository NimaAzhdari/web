namespace MvcRazor.Models;

public class Basket
{   
    public List<OrderItem> Items { get; set; } = [];
    public int NumberOfItems => Items.Sum(x => x.Quantity);

}
