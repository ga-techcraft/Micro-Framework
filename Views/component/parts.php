<div class="container my-4">
    <div id="parts-list" class="row g-4"></div>
    <nav class="mt-4 d-flex justify-content-center" aria-label="Page navigation">
        <ul id="pagination" class="pagination"></ul>
    </nav>
</div>

<script>
    function escapeHTML(str) {
        return String(str ?? "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function createCard(part) {
        return `
    <div class="col-md-4">
      <div class="card h-100 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">${escapeHTML(part.name)}</h5>
          <h6 class="card-subtitle mb-2 text-muted">${escapeHTML(part.type)} - ${escapeHTML(part.brand)}</h6>
          <p class="card-text small">
            <strong>Model:</strong> ${escapeHTML(part.model_number)}<br />
            <strong>Release Date:</strong> ${escapeHTML(part.release_date)}<br />
            <strong>Description:</strong> ${escapeHTML(part.description)}<br />
            <strong>Performance Score:</strong> ${escapeHTML(part.performance_score)}<br />
            <strong>Market Price:</strong> $${escapeHTML(part.market_price)}<br />
            <strong>RSM:</strong> $${escapeHTML(part.rsm)}<br />
            <strong>Power Consumption:</strong> ${escapeHTML(part.power_consumptionw)}W<br />
            <strong>Dimensions:</strong> ${escapeHTML(part.lengthm)}m x ${escapeHTML(part.widthm)}m x ${escapeHTML(part.heightm)}m<br />
            <strong>Lifespan:</strong> ${escapeHTML(part.lifespan)} years<br />
          </p>
          <p class="card-text"><small class="text-muted">Last updated on ${escapeHTML(part.updated_at)}</small></p>
        </div>
      </div>
    </div>
  `;
    }

    function renderPagination(current, total) {
        const pagination = document.getElementById("pagination");
        pagination.innerHTML = "";

        const range = 2; // 現在ページの前後に表示する数
        const showPages = [];

        // 先頭
        showPages.push(1);

        // 前後ページ
        for (let i = current - range; i <= current + range; i++) {
            if (i > 1 && i < total) {
                console.log(i);
                showPages.push(i);
            }
        }

        // 末尾
        if (total > 1) {
            showPages.push(total);
        }

        // « Prev
        const prevItem = document.createElement("li");
        prevItem.className = `page-item ${current === 1 ? 'disabled' : ''}`;
        prevItem.innerHTML = `<a class="page-link" href="#" aria-label="Previous">&laquo;</a>`;
        prevItem.onclick = (e) => {
            e.preventDefault();
            if (current > 1) loadParts(current - 1);
        };
        pagination.appendChild(prevItem);

        // ページ番号 + ...
        let lastPage = 0;
        showPages.forEach(i => {
            if (i - lastPage > 1) {
                const dots = document.createElement("li");
                dots.className = "page-item disabled";
                dots.innerHTML = `<span class="page-link">…</span>`;
                pagination.appendChild(dots);
            }

            const item = document.createElement("li");
            item.className = `page-item ${i === current ? 'active' : ''}`;
            item.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            item.onclick = (e) => {
                e.preventDefault();
                loadParts(i);
            };
            pagination.appendChild(item);

            lastPage = i;
        });

        // » Next
        const nextItem = document.createElement("li");
        nextItem.className = `page-item ${current === total ? 'disabled' : ''}`;
        nextItem.innerHTML = `<a class="page-link" href="#" aria-label="Next">&raquo;</a>`;
        nextItem.onclick = (e) => {
            e.preventDefault();
            if (current < total) loadParts(current + 1);
        };
        pagination.appendChild(nextItem);
    }

    function loadParts(page = 1) {
        fetch(`api/types?type=CPU&page=${page}`)
            .then(res => res.json())
            .then(data => {
                const list = document.getElementById("parts-list");
                list.innerHTML = data.parts.map(createCard).join("");
                renderPagination(data.page, data.totalPages);
            });
    }

    window.addEventListener("DOMContentLoaded", () => {
        loadParts(1);
    });
</script>