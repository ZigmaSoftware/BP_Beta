<style>
.h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
    color: #323a46;
}
</style>
<div class="row">
    <!-- start chat users-->
    <div class="col-xl-3 col-lg-4">
        <div class="card">
            <div class="card-body">

                <div class="media mb-3">
                    <img src="<?php echo $_SESSION['user_image']; ?>" class="mr-2 rounded-circle" height="42" alt="<?php echo $_SESSION['user_name']; ?>">
                    <div class="media-body">
                        <h5 class="mt-0 mb-0 font-15">
                            <a href="javascript:void(0);" class="text-reset"><?php echo $_SESSION['user_name']; ?></a>
                        </h5>
                        <p class="mt-1 mb-0 text-muted font-14">
                            <small class="mdi mdi-circle text-success"></small> Online
                        </p>
                    </div>
                    <!-- <div>
                        <a href="javascript: void(0);" class="text-reset font-20">
                            <i class="mdi mdi-cog-outline"></i>
                        </a>
                    </div> -->
                </div>

                <!-- start search box -->
                <!-- <form class="search-bar mb-3">
                    <div class="position-relative">
                        <input type="text" class="form-control form-control-light" placeholder="People, groups &amp; messages...">
                        <span class="mdi mdi-magnify"></span>
                    </div>
                </form> -->
                <!-- end search box -->

                <!-- <h6 class="font-13 text-muted text-uppercase">Group Chats</h6>
                <div class="p-2">
                    <a href="javascript: void(0);" class="text-reset mb-2 d-block">
                        <i class="mdi mdi-checkbox-blank-circle-outline mr-1 text-success"></i>
                        <span class="mb-0 mt-1">App Development</span>
                    </a>

                    <a href="javascript: void(0);" class="text-reset mb-2 d-block">
                        <i class="mdi mdi-checkbox-blank-circle-outline mr-1 text-warning"></i>
                        <span class="mb-0 mt-1">Office Work</span>
                    </a>
                </div> -->

                <h6 class="font-13 text-muted text-uppercase mb-2">Contacts</h6>

                <!-- users -->
                    <div class="row">
                        <div class="col">
                            <div data-simplebar="init" style="max-height: 375px">
                                <div class="simplebar-wrapper" style="margin: 0px;">
                                    <div class="simplebar-height-auto-observer-wrapper">
                                    <div class="simplebar-height-auto-observer"></div>
                                    </div>
                                    <div class="simplebar-mask">
                                    <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                        <div class="simplebar-content-wrapper" style="height: auto; overflow: hidden scroll;">
                                            <div class="simplebar-content" style="padding: 0px;" id="contacts_list">
                                                <!-- Contact List Goes Here -->
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="simplebar-placeholder" style="width: auto; height: 654px;"></div>
                                </div>
                                <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                                    <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                                </div>
                                <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                                    <div class="simplebar-scrollbar" style="height: 215px; transform: translate3d(0px, 135px, 0px); display: block;"></div>
                                </div>
                            </div>
                            <!-- end slimscroll-->
                        </div>
                    <!-- End col -->
                    </div>
                <!-- end users -->
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div>
    <!-- end chat users-->

    <!-- chat area -->
    <div class="col-xl-9 col-lg-8">

        <div class="card">
            <div class="card-body py-2 px-3 border-bottom border-light">
                <div class="media py-1">
                    <img src="" class="mr-2 rounded-circle" height="36" alt="" id="receiver_img">
                    <div class="media-body">
                        <h5 class="mt-0 mb-0 font-15">
                            <a href="javascript:void(0)" class="text-reset" id="dis_receiver_name">Test</a>
                        </h5>
                        <input type="hidden" name="receiver_id" id="receiver_id" value="">
                        <input type="hidden" name="receiver_name" id="receiver_name" value="">
                        <p class="mt-1 mb-0 text-muted font-12">
                            <small class="mdi mdi-circle text-success" id="receiver_status_icon"></small> <span id="receiver_status">Online</span>
                        </p>
                    </div>
                    <!-- <div>
                        <a href="javascript: void(0);" class="text-reset font-19 py-1 px-2 d-inline-block" data-toggle="tooltip" data-placement="top" title="" data-original-title="Voice Call">
                            <i class="fe-phone-call"></i>
                        </a>
                        <a href="javascript: void(0);" class="text-reset font-19 py-1 px-2 d-inline-block" data-toggle="tooltip" data-placement="top" title="" data-original-title="Video Call">
                            <i class="fe-video"></i>
                        </a>
                        <a href="javascript: void(0);" class="text-reset font-19 py-1 px-2 d-inline-block" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Users">
                            <i class="fe-user-plus"></i>
                        </a>
                        <a href="javascript: void(0);" class="text-reset font-19 py-1 px-2 d-inline-block" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete Chat">
                            <i class="fe-trash-2"></i>
                        </a>
                    </div> -->
                </div>
            </div>
            <div class="card-body">
                <ul class="conversation-list" data-simplebar="init" style="max-height: 460px"><div class="simplebar-wrapper" style="margin: 0px -15px;"><div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div><div class="simplebar-mask"><div class="simplebar-offset" style="right: 0px; bottom: 0px;"><div class="simplebar-content-wrapper" style="height: auto; overflow: hidden scroll;"><div class="simplebar-content" style="padding: 0px 15px;" id="chat_list">

                </div></div></div></div><div class="simplebar-placeholder" style="width: auto; height: 889px;"></div></div><div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="width: 0px; display: none;"></div></div><div class="simplebar-track simplebar-vertical" style="visibility: visible;"><div class="simplebar-scrollbar" style="height: 238px; transform: translate3d(0px, 0px, 0px); display: block;"></div></div></ul>

                <div class="row">
                    <div class="col">
                        <div class="mt-2 bg-light p-3 rounded">
                            <form class="needs-validation" novalidate="" name="chat-form" id="chat-form">
                                <div class="row">
                                    <div class="col mb-2 mb-sm-0">
                                        <input type="text" class="form-control border-0" placeholder="Enter your text" id="message" name="message" required="">
                                        <div class="invalid-feedback">
                                            Please enter your messsage
                                        </div>
                                    </div>
                                    <div class="col-sm-auto">
                                        <div class="btn-group">
                                            <!-- <a href="#" class="btn btn-light"><i class="fe-paperclip"></i></a> -->
                                            <button type="button" onclick="in_message();" class="btn btn-success chat-send btn-block"><i class="fe-send"></i></button>
                                        </div>
                                    </div> <!-- end col -->
                                </div> <!-- end row-->
                            </form>
                        </div> 
                    </div> <!-- end col-->
                </div>
                <!-- end row -->
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div>
    <!-- end chat area-->

</div>