namespace SmartERP.AdvancedModule.Models
{
    public class SalesReport
    {
        public int Id { get; set; }
        public DateTime ReportDate { get; set; }
        public string Period { get; set; } = string.Empty; // "daily", "weekly", "monthly"
        public decimal TotalRevenue { get; set; }
        public decimal TotalSales { get; set; }
        public decimal TotalRentals { get; set; }
        public int TransactionCount { get; set; }
        public int ProductsSold { get; set; }
        public decimal AverageOrderValue { get; set; }
        public string TopSellingCategory { get; set; } = string.Empty;
        public decimal GrowthRate { get; set; }
        public string GeneratedBy { get; set; } = string.Empty;
        public DateTime CreatedAt { get; set; }
    }

    public class ProductPerformance
    {
        public int Id { get; set; }
        public int ProductId { get; set; }
        public string ProductName { get; set; } = string.Empty;
        public string SKU { get; set; } = string.Empty;
        public int TotalQuantitySold { get; set; }
        public decimal TotalRevenue { get; set; }
        public int TotalTransactions { get; set; }
        public decimal ProfitMargin { get; set; }
        public string PerformanceRating { get; set; } = string.Empty; // "Excellent", "Good", "Average", "Poor"
        public DateTime LastSoldDate { get; set; }
    }

    public class EmployeePerformance
    {
        public int Id { get; set; }
        public int EmployeeId { get; set; }
        public string EmployeeName { get; set; } = string.Empty;
        public string Position { get; set; } = string.Empty;
        public int TotalTransactionsHandled { get; set; }
        public decimal TotalRevenue { get; set; }
        public double AttendanceRate { get; set; }
        public double CustomerSatisfactionScore { get; set; }
        public string PerformanceGrade { get; set; } = string.Empty; // "A", "B", "C", "D"
        public DateTime EvaluationPeriod { get; set; }
    }
}