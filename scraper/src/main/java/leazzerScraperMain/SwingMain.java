package leazzerScraperMain;

import javax.swing.*;

import java.awt.*;
import java.awt.event.ActionListener;
import java.awt.event.ActionEvent;

public class SwingMain implements Runnable  {
    private final JButton submit = new JButton();
    private final JTextField adminUser = new JTextField();
    private final JTextField adminPass = new JPasswordField();
    private final JCheckBox enableLogFile = new JCheckBox();

    private final JTextField cityPin = new JTextField();

    public void run() {
        JFrame f = new JFrame("Fetch and Scrape !");

        f.setDefaultCloseOperation(WindowConstants.EXIT_ON_CLOSE);

        f.setLayout(new GridLayout(5, 2));

        f.add(new JLabel("Admin Username: "));
        f.add(adminUser);

        f.add(new JLabel("Admin Password: "));
        f.add(adminPass);

        f.add(new JLabel("Enable File logging: "));
        f.add(enableLogFile);

        f.add(new JLabel("City / Zip : "));
        f.add(cityPin);

        makeButton(submit);
        f.add(submit);
        f.pack();

        f.setVisible(true);
    }

    private JButton makeButton(JButton sb) {
        sb.setText("Fetch and scrape ...");
        sb.setBounds(40, 40, 100, 30);
        sb.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent e) {
                String[] cityPinArr = new String[1];
                cityPinArr[0] = cityPin.getText();
                MainImplementation.mainImpl(cityPinArr, enableLogFile.isSelected());
            }
        });
        return sb;
    }

    public static void swingMainEntry() {
        SwingMain se = new SwingMain();

        SwingUtilities.invokeLater(se);
    }
}
