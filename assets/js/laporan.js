document.addEventListener("DOMContentLoaded",()=>{

    const table=document.getElementById("dataTable");
    const search=document.getElementById("searchInput");
    const show=document.getElementById("showData");

    if(table && search){
        const rows=table.querySelectorAll("tbody tr");

        search.addEventListener("keyup",function(){
            const keyword=this.value.toLowerCase();

            rows.forEach(row=>{
                row.style.display=row.innerText.toLowerCase().includes(keyword) ? "" : "none";
            });
        });
    }

    if(table && show){
        const rows=table.querySelectorAll("tbody tr");

        const refresh=()=>{
            const total=parseInt(show.value);

            rows.forEach((row,index)=>{
                row.style.display=index<total ? "" : "none";
            });
        };

        show.addEventListener("change",refresh);
        refresh();
    }

});

function printReport(){
    window.print();
}