<section data-modal="form" class="modal-wrapper">
    <article class="modal-body">



        <div class="modal-main">


            <div class="location">
                <div class="location--container">

                    <div class="location--row full--height">

                    <div class="location--col-12 mobile-shadow">

                    <div class="location--row">

                        <div class="location--col-12">
    
                        <header>
                            <button class="close">
                                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    x="0px" y="0px" width="419.5px" height="297.6px" viewBox="0 0 419.5 297.6" enable-background="new 0 0 419.5 297.6"
                                    xml:space="preserve">
                                    <g opacity="0.6">
                                        <path fill="#494948" d="M209.8,163L76.9,297.6l-15.9-14.2l134.6-132.9L60.9,15.9L76.9,0l132.9,134.6L344.4,0l14.2,14.2L223.9,148.8
                                            l134.6,132.9l-15.9,15.9L209.8,163z" />
                                    </g>
                                </svg>
                            </button>
                        </header>
    
                        </div>
                    </div>  

                    <div class="location--col-12">
                        <h3 class="yellow-title">
                            <img src="<?php echo plugin_dir_url( __FILE__ );?>images/location.png" alt="">
                            בחירת נקודת איסוף ב-<span>YELLOWBOX</span>
                        </h3>
                    </div>

                    <div class="location--col-12 relative">

                            <form class="location--form" autocomplete="off">
                                <div class="autocomplete">
                                    <input id="myInput" type="text" name="Yellow">
                                    <button onClick="clearInput()" type="button" id="closeInput" class="close-input">
                                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    x="0px" y="0px" width="419.5px" height="297.6px" viewBox="0 0 419.5 297.6" enable-background="new 0 0 419.5 297.6"
                                    xml:space="preserve">
                                    <g opacity="1.0">
                                        <path fill="#494948" stroke="#494948" stroke-width="12" d="M209.8,163L76.9,297.6l-15.9-14.2l134.6-132.9L60.9,15.9L76.9,0l132.9,134.6L344.4,0l14.2,14.2L223.9,148.8
                                            l134.6,132.9l-15.9,15.9L209.8,163z" />
                                    </g>
                                </svg>
                            </button>
                                </div>
                            </form>

                            <div class="whatIsYellowbox">

                                <img src="<?php echo plugin_dir_url( __FILE__ );?>images/info.png" class="info-img">
                                <button type="button" id="yellowboxBtn"> מה זה <span>YELLOWBOX</span></button>
                                <div tabindex="0" id="Yellowbox-popup" class="whatIsYellowbox-popup">
                                    <header>
                                        <div class="whatIsYellowbox-img">
                                            <img src="<?php echo plugin_dir_url( __FILE__ );?>images/logo-yellowbox.png">
                                        </div>
                                        <span>פשוט ונוח לאסוף <br> חבילות על הדרך</span>
                                    </header>
                                    <div class="whatIsYellowbox-popup-body">
                                        <h4>
                                            YELLOWBOX הם לוקרים אלקטרונים הממוקמים בתחנות פז ברחבי הארץ
                                            ומאפשרים לשלוח ולאסוף חבילות בקלות וביעילות.
                                        </h4>
                                        <span>
                                            לקבלת החבילה ב-YELLOWBOX, בחרו בתחנת פז הקרובה אליכם. כאשר החבילה
                                            תגיע לתחנה בה בחרתם, תקבלו SMS עם קוד אישי לפתיחת הלוקר.
                                            בכל שלב תוכלו להתעדכן בסטטוס החבילה באפליקציית yellow.
                                            <br><br>
                                            נתראה בקרוב:)
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                        

                        

                        

                        <div class="location--col-12 mobile-grow">
                            <div class="location--row fixed--height mobile-grow">
                                <div class="location--col-5 location--col-12-sm  mobile-grow">
                                    <div class="yellow--sub-title">
                                        <img src="<?php echo plugin_dir_url( __FILE__ );?>images/areal.png" alt=""> תחנות פז באזור: 
                                        <span id="yellow-name"></span>
                                    </div>
                                    <div class="accordion" id="accordion">

                                    </div>
                                </div>

                                <div class="location--col-7 location--col-12-sm desktop">
                                    <div id="map"></div>
                                </div>
                            </div>

                        </div>



                        <div class="location--col-12">
                            <!-- .location--btn -->
                            <button type="button" disabled id="location--action" class="close">בחירה וסיום</button>
                        </div>

                    </div>

                </div>
            </div>


        </div>

    </article>
</section>






<?php if(!get_option('YS_google_map')){ ?>
    <!-- Optional JavaScript -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDVGOY7zNeQpLrL9hj7snCYyrMLkwgP_k4&&language=he&region=IL&"
    type="text/javascript"></script>
<?php } ?>