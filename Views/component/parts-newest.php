<div id="content"></div>

<script>
  fetch('/api/parts/newest')
  .then(res => res.json())
  .then(data => {
    console.log(data['part']);
    const content = document.getElementById('content');
  
    content.innerHTML = createCard(data['part']);
  })

  const createCard = function(part){
    return `
      <div class="card h-100 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">${part.name}</h5>
          <h6 class="card-subtitle mb-2 text-muted">${part.type} - ${part.brand}</h6>
          <p class="card-text small">
            <strong>Model:</strong> ${part.model_number}<br />
            <strong>Release Date:</strong> ${part.release_date}<br />
            <strong>Description:</strong> ${part.description}<br />
            <strong>Performance Score:</strong> ${part.performance_score}<br />
            <strong>Market Price:</strong> $${part.market_price}<br />
            <strong>RSM:</strong> $${part.rsm}<br />
            <strong>Power Consumption:</strong> ${part.power_consumptionw}W<br />
            <strong>Dimensions:</strong> ${part.lengthm}m x ${part.widthm}m x ${part.heightm}m<br />
            <strong>Lifespan:</strong> ${part.lifespan} years<br />
          </p>
          <p class="card-text"><small class="text-muted">Last updated on ${part.updated_at}</small></p>
        </div>
      </div>
    `
  }
</script>

