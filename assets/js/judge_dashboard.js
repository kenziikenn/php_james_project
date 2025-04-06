function updateCandidates(eventId) {
    // Store event ID in session and show judging content
    fetch(`../ajax/update_session.php?event_id=${eventId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show the judging content
                document.getElementById('judgingContent').style.display = 'block';
                document.getElementById('selectedEventId').value = eventId;
            }
        });
}

function updateCriteriaScores(criterionId) {
    document.getElementById('selectedCriterionId').value = criterionId;
    // Get the selected option text to extract the weight
    const selectedOption = document.getElementById('criteriaSelect').selectedOptions[0];
    const weightMatch = selectedOption.text.match(/\((\d+(?:\.\d+)?)%\)/);
    const maxWeight = weightMatch ? parseFloat(weightMatch[1]) : 100;
    
    // Update all score inputs with the new max value
    const scoreInputs = document.querySelectorAll('.score-input');
    scoreInputs.forEach(input => {
        input.max = maxWeight;
    });
}

function submitScores(event) {
    event.preventDefault();
    
    const criterionId = document.getElementById('criteriaSelect').value;
    if (!criterionId) {
        alert('Please select a criteria first');
        return;
    }

    // Validate scores against maximum weight
    const selectedOption = document.getElementById('criteriaSelect').selectedOptions[0];
    const weightMatch = selectedOption.text.match(/\((\d+(?:\.\d+)?)%\)/);
    const maxWeight = weightMatch ? parseFloat(weightMatch[1]) : 100;
    
    const scoreInputs = document.querySelectorAll('.score-input');
    let validScores = true;
    
    scoreInputs.forEach(input => {
        const score = parseFloat(input.value);
        if (score > maxWeight) {
            validScores = false;
            alert(`Score cannot exceed ${maxWeight}%`);
            return;
        }
    });

    if (!validScores) return;

    const form = document.getElementById('scoringForm');
    const formData = new FormData(form);
    formData.append('criterion_id', criterionId);

    fetch('../ajax/submit_scores.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('successMessage').style.display = 'block';
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            alert(data.message || 'Error submitting scores');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting scores');
    });
}


function saveSpecialAwardWinner(candidateId, awardId) {
    if (!candidateId) return;

    const formData = new FormData();
    formData.append('candidate_id', candidateId);
    formData.append('award_id', awardId);
    formData.append('judge_id', document.getElementById('selectedEventId').dataset.judgeId);

    fetch('../pages/save_special_award_winner.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Special award winner saved successfully!');
        } else {
            alert('Error saving special award winner');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving special award winner');
    });
}


function updateSpecialAwardSelection(awardId) {
    document.getElementById('specialAwardsTableBody').style.display = awardId ? 'table-row-group' : 'none';
}

function validatePoints(input) {
    if (input.value > 10) input.value = 10;
    if (input.value < 1) input.value = 1;
}

function submitSpecialAward(event) {
    event.preventDefault();
    
    const form = document.getElementById('specialAwardsForm');
    const formData = new FormData(form);
    
    fetch('save_special_award.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Special award scores saved successfully!');
            location.reload();
        } else {
            alert(data.message || 'Failed to save scores');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving special award scores');
    });
}
