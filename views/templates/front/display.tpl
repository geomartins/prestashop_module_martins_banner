{extends file='page.tpl'}

{block name="page_content_container"}
  <p>Martinez is so cool</p>
  <p>{$martins_banner_url}</p>

  {if isset($success)}
    {l s='This is success message'}  
  {/if}

    <form  method="post" id="mymod_frm" enctype="multipart/form-data">
					
        <input id="mymod_product" name="martinsbanner_product" type="hidden" value="3" />

    <button type="submit">Submit</button>



</form


    <div> ................................. </div>

    <input type="text" name="email" id="email" placeholder="email">
    <input type="text" name="telephone"  id="telephone" placeholder="telephone">
    <button name="submit" id="submit" >Submit Ajax</button>


    <div class="result_now">
        
    </div>


{/block}