using SmartERP.AdvancedModule.DTOs;

namespace SmartERP.AdvancedModule.Services
{
    public interface IReportService
    {
        Task<SalesReportDto> GenerateSalesReportAsync(DateTime startDate, DateTime endDate, string groupBy);
        Task<List<ProductPerformanceDto>> GetProductPerformanceAsync(DateTime startDate, DateTime endDate);
        Task<List<EmployeePerformanceDto>> GetEmployeePerformanceAsync(DateTime startDate, DateTime endDate);
        Task<DashboardSummaryDto> GetDashboardSummaryAsync();
    }
}