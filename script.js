document.getElementById('addFileInput').addEventListener('click', function() {
    // Container for the file input and delete button
    var fileInputDiv = document.createElement('div');
    fileInputDiv.className = 'file-input-div';

    // Create a new file input element
    var newFileInput = document.createElement('input');
    newFileInput.type = 'file';
    newFileInput.name = 'fileToUpload[]';
    fileInputDiv.appendChild(newFileInput);

    // Create a new delete button element
    var deleteButton = document.createElement('button');
    deleteButton.type = 'button';
    deleteButton.textContent = 'Delete';
    deleteButton.className = 'delete-file-input';
    deleteButton.onclick = function() {
        // Remove the file input div from the container
        fileInputDiv.remove();
    };
    fileInputDiv.appendChild(deleteButton);

    // Append the new file input div to the container
    var container = document.getElementById('fileInputContainer');
    container.appendChild(fileInputDiv);
});

function confirmDeletion(problemId)
{
  const userConfirmed = confirm('Are you sure you want to delete this problem?');

  if (userConfirmed)
  {
    // Redirect to a PHP script with the problem ID as a query parameter
    window.location.href = `delete_problem.php?problemId=${problemId}`;
  }
}
function confirmEdit(problemId)
{
    window.location.href = `edit_problem.php?problemId=${problemId}`;
}
function openModal(src)
{
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').style.display = "block";
}

function closeModal()
{
    document.getElementById('imageModal').style.display = "none";
}

function deleteGraph(graphId) {
    // Hide the graph div
    document.getElementById('graphDiv' + graphId).style.display = 'none';
    // Add the graph ID to the list of graphs to delete
    var graphsToDelete = document.getElementById('graphsToDelete').value;
    if (graphsToDelete) {
        graphsToDelete += ',';
    }
    graphsToDelete += graphId;
    document.getElementById('graphsToDelete').value = graphsToDelete;
    // Show the cancel deletion button
    document.getElementById('cancelDelete' + graphId).style.display = 'inline';
}

// JavaScript function to cancel graph deletion
function cancelDeleteGraph(graphId) {
    // Show the graph div
    document.getElementById('graphDiv' + graphId).style.display = 'block';
    // Remove the graph ID from the list of graphs to delete
    var graphsToDelete = document.getElementById('graphsToDelete').value;
    var graphsArray = graphsToDelete.split(',');
    var index = graphsArray.indexOf('' + graphId);
    if (index > -1) {
        graphsArray.splice(index, 1);
    }
    document.getElementById('graphsToDelete').value = graphsArray.join(',');
    // Hide the cancel deletion button
    document.getElementById('cancelDelete' + graphId).style.display = 'none';
}

window.MathJax = {
  tex: {
    inlineMath: [['$', '$'], ['\\[', '\\]']],
    displayMath: [['$$', '$$']],
    processEscapes: true,
    processEnvironments: true,
    macros: {
      ds: "\\displaystyle",
      bm: ["\\boldsymbol{#1}", 1],
      mathlarger : ["\\large{#1}", 1]
    }
  },
  svg: {
    fontCache: 'global'
  },
  startup: {
    ready: () => {
      console.log('MathJax is ready');
      MathJax.startup.defaultReady();
    }
  },
  options: {
    skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre'],
    ignoreHtmlClass: 'tex2jax_ignore',
    processHtmlClass: 'tex2jax_process'
  }
};

document.addEventListener('DOMContentLoaded', (event) => {
  if (MathJax.startup) {
    MathJax.startup.promise.then(() => {
      // Reprocess the entire document
      MathJax.typesetPromise();
    });
  }
});


