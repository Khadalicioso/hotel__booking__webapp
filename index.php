<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30, '/');
   header('location:index.php');
}

if(isset($_POST['check'])){

   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }
   // if the hotel has total 30 rooms
   if($total_rooms >= 30){
      $warning_msg[] = 'Rooms Are Not Available';
   }else{
      $success_msg[] = 'Rooms Are Available';
   }

}

if(isset($_POST['book'])){

   $booking_id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $rooms = $_POST['rooms'];
   $rooms = filter_var($rooms, FILTER_SANITIZE_STRING);
   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);
   $check_out = $_POST['check_out'];
   $check_out = filter_var($check_out, FILTER_SANITIZE_STRING);
   $adults = $_POST['adults'];
   $adults = filter_var($adults, FILTER_SANITIZE_STRING);
   $childs = $_POST['childs'];
   $childs = filter_var($childs, FILTER_SANITIZE_STRING);
   $total_rooms = 0;
   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);
   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   if($total_rooms >= 30){
      $warning_msg[] = 'Rooms Are Not Available';
   }else{

      $verify_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND name = ? AND email = ? AND number = ? AND rooms = ? AND check_in = ? AND check_out = ? AND adults = ? AND childs = ?");
      $verify_bookings->execute([$user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);

      if($verify_bookings->rowCount() > 0){
         $warning_msg[] = 'Room Booked Already!';
      }else{
         $book_room = $conn->prepare("INSERT INTO `bookings`(booking_id, user_id, name, email, number, rooms, check_in, check_out, adults, childs) VALUES(?,?,?,?,?,?,?,?,?,?)");
         $book_room->execute([$booking_id, $user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);
         $success_msg[] = 'Room Booked Successfully!';
      }

   }

}

if(isset($_POST['send'])){

   $id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $message = $_POST['message'];
   $message = filter_var($message, FILTER_SANITIZE_STRING);

   $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $verify_message->execute([$name, $email, $number, $message]);

   if($verify_message->rowCount() > 0){
      $warning_msg[] = 'Message Sent Already!';
   }else{
      $insert_message = $conn->prepare("INSERT INTO `messages`(id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$id, $name, $email, $number, $message]);
      $success_msg[] = 'Message Send Successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="home" id="home">
   <div class="swiper home-slider">
      <div class="swiper-wrapper">
         <div class="box swiper-slide">
            <img src="images/home-img-1.jpg" alt="">
            <div class="flex">
               <h3>Luxurious Rooms</h3>
               <a href="#availability" class="btn">Check Availability</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/home-img-2.jpg" alt="">
            <div class="flex">
               <h3>Luxurious Dining and Wine Room</h3>
               <a href="#reservation" class="btn">Make A Reservation</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/home-img-3.jpg" alt="">
            <div class="flex">
               <h3>Luxurious Halls</h3>
               <a href="#contact" class="btn">Contact Us</a>
            </div>
         </div>
      </div>
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
   </div>
</section>
<section class="availability" id="availability">
   <form action="" method="post">
      <div class="flex">
         <div class="box">
            <p>Check In</p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>Check Out</p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>Adults</p>
            <select name="adults" class="input" required>
               <option value="1">1 Adult</option>
               <option value="2">2 Adult</option>
               <option value="3">3 Adult</option>
               <option value="4">4 Adult</option>
               <option value="5">5 Adult</option>
               <option value="6">6 Adult</option>
            </select>
         </div>
         <div class="box">
            <p>Children</p>
            <select name="childs" class="input" required>
               <option value="-">0 Children</option>
               <option value="1">1 Children</option>
               <option value="2">2 Children</option>
               <option value="3">3 Children</option>
               <option value="4">4 Children</option>
               <option value="5">5 Children</option>
               <option value="6">6 Children</option>
            </select>
         </div>
         <div class="box">
            <p>Rooms</p>
            <select name="rooms" class="input" required>
               <option value="1">1 Room</option>
               <option value="2">2 Room</option>
               <option value="3">3 Room</option>
               <option value="4">4 Room</option>
               <option value="5">5 Room</option>
               <option value="6">6 Room</option>
            </select>
         </div>
      </div>
      <input type="submit" value="check availability" name="check" class="btn">
   </form>
</section>
<section class="about" id="about">
   <div class="row">
      <div class="image">
         <img src="images/about-1.jpg" alt="">
      </div>
      <div class="content">
         <h3>Employee Of The Month</h3>
         <p>Thanks to Franz Jeremy Señora for being a certified and greatest Kupal here at KUPAL HOTEL.</p>
         <a href="#reservation" class="btn">Make A Reservation</a>
      </div>
   </div>
   <div class="row revers">
      <div class="image">
         <img src="images/about-2.jpg" alt="">
      </div>
      <div class="content">
         <h3>Delicious Foods</h3>
         <p>Let’s tantalize your taste buds with an introduction to the delectable cuisine you’ll find at KUPAL HOTEL. Our culinary offerings are a delightful blend of local flavors and international influences.</p>
         <a href="#contact" class="btn">Contact Us</a>
      </div>
   </div>
   <div class="row">
      <div class="image">
         <img src="images/about-3.jpg" alt="">
      </div>
      <div class="content">
         <h3>Swimming Pool</h3>
         <p>You step onto the pool deck, and your eyes widen. Before you stretches an infinity pool—a liquid canvas merging seamlessly with the horizon. The water spills over the edge, creating an illusion that you’re swimming toward eternity. As you glide through the crystal-clear depths, the sky and water become one. It’s a symphony of blues—a crescendo of tranquility.</p>
         <a href="#availability" class="btn">Check Availability</a>
      </div>
   </div>
</section>
<section class="services">
   <div class="box-container">
      <div class="box">
         <img src="images/icon-1.png" alt="">
         <h3>Food & Beverages</h3>
         <p>Food and beverage aren’t just about taste; they’re about connection. It’s the anniversary toast, the laughter over tapas, the shared dessert fork. It’s the way a perfectly brewed cappuccino warms your hands on a chilly morning. These moments weave into the fabric of your stay, becoming stories you’ll tell long after checkout.</p>
      </div>
      <div class="box">
         <img src="images/icon-2.png" alt="">
         <h3>Outdoor Dining</h3>
         <p>As you dine outdoors, listen. The birds provide the melody—their songs weaving through conversations. The leaves rustle—a gentle percussion. And laughter—the universal language—fills the air. It’s a symphony conducted by nature, and you’re part of the ensemble.</p>
      </div>
      <div class="box">
         <img src="images/icon-3.png" alt="">
         <h3>Beach View</h3>
         <p>Imagine stepping onto our lushly landscaped grounds—a five-acre canvas of tropical beauty. Our family-run hotel embraces you like a warm breeze. We’re not just hosts; we’re storytellers, curators of memories.</p>
      </div>
      <div class="box">
         <img src="images/icon-4.png" alt="">
         <h3>Amenities</h3>
         <p>Our lushly landscaped grounds span five acres—a verdant haven where tropical beauty meets tranquility. Stroll through our gardens, breathe in the fresh air, and let the vibrant flora surround you.</p>
      </div>
      <div class="box">
         <img src="images/icon-5.png" alt="">
         <h3>Swimming Pool</h3>
         <p>Our pool isn’t just about laps and leisure—it’s a social hub. Swim to the edge, and there it is: the poolside bar. Sip a piña colada or a cocktail, toes dipped in water. Chat with fellow guests—their laughter echoing off the tiles. Maybe you’ll strike up a conversation with someone from across the world, sharing stories as the sun dips below the horizon.</p>
      </div>
      <div class="box">
         <img src="images/icon-6.png" alt="">
         <h3>Resort Beach</h3>
         <p>Our beachfront is your canvas for relaxation. Sink into a plush lounger, feel the warm sand between your toes, and let the rhythmic sound of the waves lull you into bliss. The sun umbrellas provide a cozy nook for reading, napping, or simply gazing at the horizon.</p>
      </div>
   </div>
</section>
<section class="reservation" id="reservation">
   <form action="" method="post">
      <h3>Make A Reservation</h3>
      <div class="flex">
         <div class="box">
            <p>Full Name</p>
            <input type="text" name="name" maxlength="50" required placeholder="Enter Your Full Name" class="input">
         </div>
         <div class="box">
            <p>Email Address</p>
            <input type="email" name="email" maxlength="50" required placeholder="Enter Your Email Address" class="input">
         </div>
         <div class="box">
            <p>Contact Number</p>
            <input type="number" name="number" maxlength="10" min="0" max="9999999999" required placeholder="Enter Your Contact Number" class="input">
         </div>
         <div class="box">
            <p>Rooms</p>
            <select name="rooms" class="input" required>
               <option value="1" selected>1 Room</option>
               <option value="2">2 Room</option>
               <option value="3">3 Room</option>
               <option value="4">4 Room</option>
               <option value="5">5 Room</option>
               <option value="6">6 Room</option>
            </select>
         </div>
         <div class="box">
            <p>Check In</p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>Check Out></p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>Adults</p>
            <select name="adults" class="input" required>
               <option value="1" selected>1 Adult</option>
               <option value="2">2 Adult</option>
               <option value="3">3 Adult</option>
               <option value="4">4 Adult</option>
               <option value="5">5 Adult</option>
               <option value="6">6 Adult</option>
            </select>
         </div>
         <div class="box">
            <p>Children</p>
            <select name="childs" class="input" required>
               <option value="0" selected>0 Children</option>
               <option value="1">1 Children</option>
               <option value="2">2 Children</option>
               <option value="3">3 Children</option>
               <option value="4">4 Children</option>
               <option value="5">5 Children</option>
               <option value="6">6 Children</option>
            </select>
         </div>
      </div>
      <input type="submit" value="book now" name="book" class="btn">
   </form>
</section>
<section class="gallery" id="gallery">
   <div class="swiper gallery-slider">
      <div class="swiper-wrapper">
         <img src="images/gallery-1.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-2.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-3.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-4.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-5.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-6.jpg" class="swiper-slide" alt="">
      </div>
      <div class="swiper-pagination"></div>
   </div>
</section>
<section class="contact" id="contact">
   <div class="row">
      <form action="" method="post">
         <h3>Feel free to reach out to us if you have any concerns.</h3>
         <input type="text" name="name" required maxlength="50" placeholder="Enter Your Full Name" class="box">
         <input type="email" name="email" required maxlength="50" placeholder="Enter Your Email Address" class="box">
         <input type="number" name="number" required maxlength="10" min="0" max="9999999999" placeholder="Enter Your Phone Number" class="box">
         <textarea name="message" class="box" required maxlength="1000" placeholder="Message/Concern" cols="30" rows="10"></textarea>
         <input type="submit" value="send message" name="send" class="btn">
      </form>
      <div class="faq">
         <h3 class="title">Frequently Asked Questions</h3>
         <div class="box active">
            <h3>How can I make changes to my booking at KUPAL HOTEL?</h3>
            <p>If you need to modify your reservation or have any questions about your booking, please reach out to our friendly reservations team. You can find their contact details on our website.</p>
         </div>
         <div class="box">
            <h3>What are the check-in and check-out times at KUPAL HOTEL?</h3>
            <p>Check-in time at KUPAL HOTEL is typically from 12 pm onwards. However, if you arrive early, feel free to explore the resort’s facilities while your accommodation is being prepared.</p>
            <p>Check-out time is usually by 6 am on the day of departure. If you need a late check-out, contact our reservations team in advance (additional fees may apply).</p>
         </div>
         <div class="box">
            <h3>What leisure facilities are available to guests at KUPAL HOTEL?</h3>
            <p>During your stay, you’ll have access to our swimming pool, steam rooms, and saunas—all included in your holiday package.</p>
            <p>If you’re into sports, we also have tennis and basketball courts. Just inquire at the resort for any equipment rental costs.</p>
         </div>
         <div class="box">
            <h3>Is Wi-Fi available at KUPAL HOTEL?</h3>
            <p>Yes, we provide Wi-Fi in public areas and, in most cases, within your accommodation. Keep in mind that due to our rural location, the signal may occasionally be intermittent.</p>
         </div>
         <div class="box">
            <h3>Where can I park my car at KUPAL HOTEL?</h3>
            <p>Complimentary parking is available on-site. Depending on the layout of our resort, it might be right next to your accommodation or in a nearby free car park.</p>
         </div>
         <div class="box">
            <h3>Are bedlinen and towels provided at KUPAL HOTEL?</h3>
            <p>Absolutely! We include all bedlinen and towels in the price of your holiday. If you need extra changes or towels, just ask at our reception.</p>
         </div>
         <div class="box">
            <h3>Are there any hidden charges once I’ve paid for my stay at KUPAL HOTEL?</h3>
            <p>Once you’ve paid for your holiday, there shouldn’t be any surprise charges. However, if you have specific requests (early check-in, late check-out), it’s best to let us know in advance.</p>
         </div>
      </div>
   </div>
</section>
<section class="reviews" id="reviews">
   <div class="swiper reviews-slider">
      <div class="swiper-wrapper">
         <div class="swiper-slide box">
            <img src="images/pic-1.png" alt="">
            <h3>Jerick Ybarreta</h3>
            <p>KUPAL HOTEL is where dreams come true. The beach view from our suite? Jaw-dropping. The infinity pool? Instagram-worthy. And the moonlit dinners? Pure romance. We’ll be back—count on it!</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-2.png" alt="">
            <h3>Antonette Bayan</h3>
            <p>From the warm welcome to the farewell hugs, KUPAL’s staff made us feel like family. The beachfront loungers were our front-row seats to paradise. And that beachside massage? Blissful. Can we move in permanently?</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-3.png" alt="">
            <h3>Mico Intertas</h3>
            <p>KUPAL’s beach is our happy place. Crystal-clear waters, powdery sand, and sun umbrellas that whisper, ‘Relax, you’re home.’ The beachfront dining? A symphony of flavors. We left our hearts here.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-4.png" alt="">
            <h3>Patricia Nicole Villanueva</h3>
            <p>At KUPAL, sunsets are a daily miracle. We’d sit on the beach, toes in the sand, as the sky turned into a canvas of oranges and pinks. The beach bar served cocktails with a side of wonder. Magical moments? Everywhere.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-5.png" alt="">
            <h3>John Powell Delos Santos</h3>
            <p>KUPAL’s suites redefine luxury. Spacious, elegantly designed, and those ocean views! We’d wake up to the sound of waves, brew coffee in our kitchen, and step onto the balcony—it felt like owning a piece of paradise.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-6.png" alt="">
            <h3>Mhel Bandibad</h3>
            <p>Our stay at KUPAL HOTEL was a chapter we’ll reread forever. The beach bonfires, the laughter by the pool, and the moonlit walks—it’s etched in our hearts. Thank you for making our vacation extraordinary!</p>
         </div>
      </div>
      <div class="swiper-pagination"></div>
   </div>
</section>

<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="js/script.js"></script>

<?php include 'components/message.php'; ?>

</body>
</html>