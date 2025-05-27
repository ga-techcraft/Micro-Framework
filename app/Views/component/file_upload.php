<h1>画像アップロード</h1>
<input id="file" type="file" name="image" accept="image/*" required>
<button id="uploadButton" type="button">アップロード</button>

<div id="view"></div>
<div id="delete"></div>

<script>
  document.querySelector('#uploadButton').addEventListener('click', function(e){
    e.preventDefault();
    const file = document.getElementById('file').files[0];
    const formData = new FormData();
    formData.append('image', file);
    formData.append('csrf_token', '<?= \Helpers\CrossSiteForgeryProtection::getToken() ?>');
    
    fetch('api/images/upload', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    // .then(response => response.text())
    .then(data => {
      // console.log(data);
      if(data.error){
        alert(data.error);
      } else {
        const viewPath = `${data.signedViewURL}`;
        const deletePath = `${data.signedDeleteURL}`;

        document.querySelector('#view').innerHTML = `<button onclick="copyToClipboard('${viewPath}')">表示用URLをコピー</button>`;
        document.querySelector('#delete').innerHTML = `<button onclick="copyToClipboard('${deletePath}')">削除用URLをコピー</button>`;

      }
    })
    .catch(error => {
      console.error('Error:', error);
    });
  });

  function copyToClipboard(path){
    navigator.clipboard.writeText(path);
  }
</script>
