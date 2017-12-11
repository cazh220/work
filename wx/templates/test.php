<script src="plugins/scripts/imagesloaded.pkgd.min.js"></script>
<script src="plugins/scripts/hammer.min.js"></script>
<script src="plugins/scripts/sequence.min.js"></script>
<script src="plugins/scripts/sequence-theme.mono.js"></script>

<div id="sequence" class="seq">
		<ul class="seq-canvas">

		  <li class="seq-step1 seq-valign seq-in">
			<div class="seq-vcenter">
			  <img data-seq class="seq-feature" src="plugins/images/bananas.png" alt="A cartoon illustration of a bunch of bananas" width="389" height="300" srcset="plugins/images/bananas.png 1x, pluginsimages/bananas@2x.png 2x" />
			  <h2 data-seq class="seq-title">Bananas</h2>
			</div>
		  </li>

		  <li class="seq-step2 seq-valign">
			<div class="seq-vcenter">
			  <img data-seq class="seq-feature" src="plugins/images/coconut.png" alt="A cartoon illustration of half a coconut" width="325" height="300" srcset="plugins/images/coconut.png 1x, plugins/images/coconut@2x.png 2x" />
			  <h2 data-seq class="seq-title">Coconut</h2>
			</div>
		  </li>

		  <li class="seq-step3 seq-valign">
			<div class="seq-vcenter">
			  <img data-seq class="seq-feature" src="plugins/images/orange.png" alt="A cartoon illustration of a round orange" width="350" height="300" srcset="plugins/images/orange.png 1x, plugins/images/orange@2x.png 2x" />
			  <h2 data-seq class="seq-title">Orange</h2>
			</div>
		  </li>
		</ul>

		<fieldset class="seq-nav" aria-label="Slider buttons" aria-controls="sequence">

		  <button type="button" class="seq-prev" aria-label="Previous">
			<img src="plugins/images/prev.svg" alt="Previous" />
		  </button>

		  <ul role="navigation" aria-label="Pagination" class="seq-pagination">
			<li class="seq-current">
			  <a href="#step1" rel="step1" title="Go to bananas">
				<img src="plugins/images/tn-bananas@2x.png" alt="Bananas" width="50" height="40" />
			  </a>
			</li>
			<li>
			  <a href="#step2" rel="step2" title="Go to coconut">
				<img src="plugins/images/tn-coconut@2x.png" alt="Coconut" width="50" height="40" />
			  </a>
			</li>
			<li>
			  <a href="#step3" rel="step3" title="Go to orange">
				<img src="plugins/images/tn-orange@2x.png" alt="Orange" width="50" height="40" />
			  </a>
			</li>
		  </ul>

		  <button type="button" class="seq-next" aria-label="Next">
			<img src="plugins/images/next.svg" alt="Next" />
		  </button>

		</fieldset>
		</div>
