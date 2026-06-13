// admin.js
document.addEventListener("DOMContentLoaded", function() {
    const tableBody = document.getElementById('dashboard-table-body');
    
    // Mock Data mimicking the provided image, but with C2C context (Buyer/Seller/Admin roles)
    const mockData = [
        {
            name: "Habiska Toranta",
            email: "michealbayh@gmail.com",
            username: "MikegnaGetcha",
            status: "Active",
            role: "Admin",
            joinedDate: "March 12, 2023",
            lastActive: "1 minute ago",
          
        },
        {
            name: "Glodi Maponda",
            email: "glodrick@upbeat.za",
            username: "ollie",
            status: "Inactive",
            role: "Buyer",
            joinedDate: "28 May, 2026",
            lastActive: "12 Days ago",
          
        },
        {
            name: "Montana",
            email: "Jowayy@yahoo.com",
            username: "dwarren3",
            status: "Banned",
            role: "Seller",
            joinedDate: "28 May, 2026",
            lastActive: "12 Days ago",
       
        },
        {
            name: "Chloe Hayes",
            email: "chloehhye@gmail.com",
            username: "chloehh",
            status: "Pending",
            role: "Buyer",
            joinedDate: "28 May, 2026",
            lastActive: "12 Days ago",
            
        },
        {
            name: "Jojo Ruski",
            email: "JojoRuski@gmail.com",
            username: "balenci",
            status: "Suspended",
            role: "Seller",
           joinedDate: "28 May, 2026",
            lastActive: "12 Days ago",
           
        },
        {
            name: "Keren Maponda",
            email: "kerenmaponda@gmail.com",
            username: "bellecl",
            status: "Active",
            role: "Moderator",
            joinedDate: "28 May, 2026",
            lastActive: "12 Days ago",
           
        },
        {
            name: "Mitchell Gattinway",
            email: "lucamich@gmail.com",
            username: "lucamich",
            status: "Active",
            role: "Buyer",
           joinedDate: "28 May, 2026",
            lastActive: "12 Days ago",
            
        }
    ];

    function getStatusClass(status) {
        switch(status.toLowerCase()) {
            case 'active': return 'status-active';
            case 'inactive': return 'status-inactive';
            case 'banned': return 'status-banned';
            case 'pending': return 'status-pending';
            case 'suspended': return 'status-suspended';
            default: return 'status-inactive';
        }
    }

    // Populate Table
    mockData.forEach(item => {
        const row = document.createElement('tr');
        
        row.innerHTML = `
            <td><input type="checkbox"></td>
            <td>
                <div class="user-name-cell">
                    ${item.name}
                </div>
            </td>
            <td>${item.email}</td>
            <td>${item.username}</td>
            <td><span class="status-badge ${getStatusClass(item.status)}">${item.status}</span></td>
            <td>${item.role}</td>
            <td>${item.joinedDate}</td>
            <td>${item.lastActive}</td>
            <td>
                <div class="action-icons">
                    <i class="fas fa-pencil-alt"></i>
                    <i class="far fa-trash-alt"></i>
                </div>
            </td>
        `;
        
        tableBody.appendChild(row);
    });
});
