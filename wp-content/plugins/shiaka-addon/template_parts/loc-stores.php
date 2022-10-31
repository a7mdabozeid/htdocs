
    <div class="bh-sl-container">
          <div class="bh-sl-form-container">
            <form id="bh-sl-user-location" method="post" action="#">
                <div class="form-input">
                  <label for="bh-sl-address"><?= __('Select City' , 'razzi') ?></label>
                  <select name="addresss" id="bh-sl-address" name="bh-sl-address">
        
                      <?php foreach($locations as $location) {?>
                          <option value="<?= trim($location)?>"><?= trim($location) ?></option>
                        <?php }?>
                  </select>
                </div>

                <button id="bh-sl-submit" type="submit"><?= __('Submit' , 'razzi') ?></button>
            </form>
          </div>
    
          <div id="bh-sl-map-container" class="bh-sl-map-container">
            <div id="bh-sl-map" class="bh-sl-map"></div>
            <div class="bh-sl-loc-list">
              <ul class="list"></ul>
            </div>
          </div>
        </div>